<?php

namespace App\Http\Controllers;

use App\Models\ExpertConsultation;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use Illuminate\Http\JsonResponse;

class ExpertConsultationController extends Controller
{
    /**
     * إرجاع كل الاستشارات السابقة
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $consultations = ExpertConsultation::where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(function ($c) {
                    $symptoms = is_array($c->symptoms)
                        ? $c->symptoms
                        : (json_decode($c->symptoms ?? '[]', true) ?: []);

                    return [
                        'id'              => $c->id,
                        'user_id'         => $c->user_id,
                        'symptoms'        => $symptoms,
                        'diagnosis_label' => $c->diagnosis_label,
                        'risk_level'      => $c->risk_level,
                        'risk_score'      => $c->risk_score,
                        'recommendation'  => $c->recommendation,
                        'created_at'      => $c->created_at,
                    ];
                });

            AuditLogger::log('Viewed expert consultation list');

            return response()->json([
                'success' => true,
                'data'    => $consultations,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('ExpertConsultation Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * حفظ استشارة جديدة + تشغيل النظام الخبير
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'symptoms'   => 'required|array|min:1',
                'symptoms.*' => 'string',
            ]);

            $symptoms = $validated['symptoms'];

            // تشغيل النظام الخبير
            $diag = $this->runExpertSystemFromSymptoms($symptoms);

            // تخزين الاستشارة
            $consultation = ExpertConsultation::create([
                'user_id'         => $user->id,
                'symptoms'        => $symptoms,
                'recommendation'  => $diag['recommendation'],
                'diagnosis_label' => $diag['diagnosis_label'],
                'risk_level'      => $diag['risk_level'],
                'risk_score'      => $diag['risk_score'],
            ]);

            AuditLogger::log('Created expert consultation with expert system');

            return response()->json([
                'message'       => 'Consultation analyzed and saved successfully',
                'consultation'  => [
                    'id'              => $consultation->id,
                    'user_id'         => $consultation->user_id,
                    'symptoms'        => $symptoms,
                    'diagnosis_label' => $diag['diagnosis_label'],
                    'risk_level'      => $diag['risk_level'],
                    'risk_score'      => $diag['risk_score'],
                    'recommendation'  => $diag['recommendation'],
                    'created_at'      => $consultation->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('ExpertConsultation Store Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * آخر استشارة واحدة
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $c = ExpertConsultation::where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$c) {
                return response()->json(['message' => 'No consultation found'], 404);
            }

            $symptoms = is_array($c->symptoms)
                ? $c->symptoms
                : (json_decode($c->symptoms ?? '[]', true) ?: []);

            AuditLogger::log('Viewed latest expert consultation');

            return response()->json([
                'success' => true,
                'data' => [
                    'id'              => $c->id,
                    'user_id'         => $c->user_id,
                    'symptoms'        => $symptoms,
                    'diagnosis_label' => $c->diagnosis_label,
                    'risk_level'      => $c->risk_level,
                    'risk_score'      => $c->risk_score,
                    'recommendation'  => $c->recommendation,
                    'created_at'      => $c->created_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('ExpertConsultation Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * منطق النظام الخبير
     */
    private function runExpertSystemFromSymptoms(array $symptoms): array
    {
        $has = function (array $keywords) use ($symptoms): bool {
            foreach ($keywords as $k) {
                foreach ($symptoms as $s) {
                    if (mb_stripos($s, $k) !== false) {
                        return true;
                    }
                }
            }
            return false;
        };

        $chest_pain   = $has(['ألم في الصدر']);
        $pressing_pain= $has(['ألم ضاغط']);
        $sharp_pain   = $has(['ألم حاد']);
        $radiation    = $has(['ينتشر إلى الذراع', 'الذراع الأيسر', 'الفك', 'الظهر']);
        $sweating     = $has(['تعرق']);
        $dyspnea      = $has(['ضيق تنفس']);
        $palpitations = $has(['خفقان']);
        $fainting     = $has(['دوار', 'إغماء', 'فقدان الوعي']);
        $fatigue      = $has(['إرهاق', 'تعب']);
        $exertion_dyspnea = $has(['ضيق تنفس مع مجهود بسيط']);
        $swelling     = $has(['تورم القدمين', 'تورم الساقين']);
        $lying        = $has(['يزداد عند الاستلقاء']);
        $frequent     = $has(['متكرر', 'أكثر من مرة أسبوعياً']);

        $diagnosis_label = 'أعراض طبيعية أو بسيطة';
        $risk_level      = 'low';
        $risk_score      = 0.2;
        $recommendation  = 'نوصي بالراحة ومتابعة الأعراض، وإذا استمرت أو ساءت يرجى مراجعة الطبيب.';

        if ($chest_pain) {
            if ($pressing_pain && $radiation && $sweating) {
                $diagnosis_label = 'نقص تروية يؤدي لاحتشاء عضلة القلب';
                $risk_level      = 'high';
                $risk_score      = 0.95;
                $recommendation  = 'حالة إسعافية محتملة. نوصي بطلب الإسعاف فوراً أو مراجعة الطوارئ بشكل عاجل.';
            } elseif ($pressing_pain && $radiation) {
                $diagnosis_label = 'خناق صدري غير مستقر';
                $risk_level      = 'high';
                $risk_score      = 0.85;
                $recommendation  = 'نقص تروية محتمل. نوصي بمراجعة طبيب القلب بشكل عاجل لإجراء تخطيط وتحاليل.';
            } elseif ($pressing_pain) {
                $diagnosis_label = 'خناق صدري مستقر';
                $risk_level      = 'medium';
                $risk_score      = 0.6;
                $recommendation  = 'نوصي بتنظيم الجهد ومراجعة طبيب القلب قريباً لتقييم الشرايين التاجية.';
            } elseif ($sharp_pain && $dyspnea) {
                $diagnosis_label = 'التهاب غشاء القلب أو مشكلة رئوية';
                $risk_level      = 'medium';
                $risk_score      = 0.6;
                $recommendation  = 'نوصي بمراجعة طبيب باطني/قلب مع تصوير صدري وتخطيط قلب.';
            } else {
                $diagnosis_label = 'ألم صدري غير قلبي غالباً';
                $risk_level      = 'low';
                $risk_score      = 0.3;
                $recommendation  = 'الألم قد يكون عضلياً أو هضمياً. إذا استمر أو ازداد نوصي بمراجعة الطبيب.';
            }
        } elseif ($dyspnea) {
            if ($palpitations && $fainting) {
                $diagnosis_label = 'اضطراب نظم القلب خطير محتمل';
                $risk_level      = 'high';
                $risk_score      = 0.9;
                $recommendation  = 'نوصي بمراجعة الطوارئ أو طبيب القلب بشكل عاجل لإجراء تخطيط قلب فوري.';
            } elseif ($exertion_dyspnea && $swelling && $lying) {
                $diagnosis_label = 'قصور القلب الأيسر';
                $risk_level      = 'high';
                $risk_score      = 0.85;
                $recommendation  = 'نوصي بتقييم عاجل لوظيفة القلب وضبط السوائل.';
            } elseif ($exertion_dyspnea && $swelling) {
                $diagnosis_label = 'قصور القلب';
                $risk_level      = 'high';
                $risk_score      = 0.85;
                $recommendation  = 'نوصي بمراجعة طبيب القلب قريباً لتقييم وظيفة القلب.';
            } elseif ($exertion_dyspnea) {
                $diagnosis_label = 'ضيق تنفس يحتاج تقييم';
                $risk_level      = 'medium';
                $risk_score      = 0.6;
                $recommendation  = 'نوصي بفحص سريري شامل (قلب، رئتين، دم) لتحديد السبب.';
            } else {
                $diagnosis_label = 'ضيق تنفس خفيف';
                $risk_level      = 'low';
                $risk_score      = 0.3;
                $recommendation  = 'نوصي بالمراقبة، وإذا ازداد أو ترافق مع أعراض أخرى يرجى مراجعة الطبيب.';
            }
        } elseif ($palpitations) {
            if ($fainting) {
                $diagnosis_label = 'اضطراب نظم القلب مع دوار/إغماء';
                $risk_level      = 'high';
                $risk_score      = 0.9;
                $recommendation  = 'نوصي بمراجعة طبيب القلب بشكل عاجل وإجراء تخطيط قلب وهولتر.';
            } elseif ($frequent) {
                $diagnosis_label = 'اضطراب نظم القلب';
                $risk_level      = 'medium';
                $risk_score      = 0.6;
                $recommendation  = 'نوصي بإجراء تخطيط قلب وهولتر لتقييم نوع الاضطراب.';
            } else {
                $diagnosis_label = 'خفقان طبيعي غالباً بصحة جيدة';
                $risk_level      = 'low';
                $risk_score      = 0.2;
                $recommendation  = 'نوصي بتقليل المنبهات ومراقبة الأعراض ومراجعة الطبيب عند اللزوم.';
            }
        } elseif ($fatigue) {
            $diagnosis_label = 'إرهاق قد يكون مرتبطاً بالقلب أو أعضاء أخرى';
            $risk_level      = 'medium';
            $risk_score      = 0.4;
            $recommendation  = 'نوصي بإجراء فحوص عامة (دم، غدة درقية، قلب) لتحديد السبب.';
        } else {
            $diagnosis_label = 'لا توجد أعراض قلبية واضحة';
            $risk_level      = 'low';
            $risk_score      = 0.1;
            $recommendation  = 'الوضع مطمئن مبدئياً مع مراقبة أي أعراض جديدة.';
        }

        return [
            'diagnosis_label' => $diagnosis_label,
            'risk_level'      => $risk_level,
            'risk_score'      => $risk_score,
            'recommendation'  => $recommendation,
        ];
    }
}
