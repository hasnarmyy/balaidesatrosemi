<?php

namespace App\Helpers;

class FaceHelper
{
    /**
     * Threshold similarity cosine untuk wajah sama.
     * 0.97 = sangat ketat, mengurangi false positive (wajah beda dianggap sama).
     */
    const COSINE_THRESHOLD = 0.97;
    const MAX_DISTANCE = 0.6;

    /**
     * Minimum jumlah sample enrollment yang harus cocok.
     * Wajah beda jarang cocok dengan BEBERAPA foto enrollment sekaligus.
     */
    const MIN_SAMPLES_MUST_MATCH = 2;

    /**
     * Cek apakah gender live tidak cocok dengan enrollment.
     * Membantu mencegah false positive (mis. laki-laki enroll, perempuan absen).
     * Returns true jika mismatch (harus ditolak).
     */
    public static function isGenderMismatch(array $enrolledGenders, ?string $liveGender): bool
    {
        $live = $liveGender ? strtolower(trim($liveGender)) : null;
        if ($live === '' || !in_array($live, ['male', 'female'], true)) {
            return false; // Skip check jika live gender tidak tersedia
        }

        $valid = array_filter(array_map(function ($g) {
            $g = $g ? strtolower(trim($g)) : null;
            return in_array($g, ['male', 'female'], true) ? $g : null;
        }, $enrolledGenders));

        if (empty($valid)) {
            return false; // Skip jika enrollment tidak punya gender (data lama)
        }

        $counts = array_count_values($valid);
        arsort($counts);
        $enrolledGender = array_key_first($counts);

        return $enrolledGender !== $live;
    }

    /**
     * Calculate cosine similarity between two embedding vectors
     */
    public static function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || count($a) === 0) {
            return 0.0;
        }

        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        for ($i = 0; $i < count($a); $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / ($normA * $normB);
    }

    /**
     * Verify embedding against multiple enrollment samples.
     * Memakai logika konsensus: minimal MIN_SAMPLES_MUST_MATCH sample harus
     * melewati threshold, agar wajah orang lain tidak lolos hanya karena
     * kebetulan mirip 1 foto enrollment.
     */
    public static function verifyEmbedding(array $liveEmbedding, array $enrollmentEmbeddings): array
    {
        $similarities = [];
        $maxSimilarity = 0;

        foreach ($enrollmentEmbeddings as $enrollSample) {
            if (is_string($enrollSample)) {
                $enrollSample = json_decode($enrollSample, true) ?? [];
            }
            if (!is_array($enrollSample) || empty($enrollSample)) {
                continue;
            }

            $sim = self::cosineSimilarity($liveEmbedding, $enrollSample);
            $similarities[] = $sim;
            if ($sim > $maxSimilarity) {
                $maxSimilarity = $sim;
            }
        }

        $totalSamples = count($similarities);
        $matchedCount = 0;
        foreach ($similarities as $s) {
            if ($s >= self::COSINE_THRESHOLD) {
                $matchedCount++;
            }
        }

        // Minimal berapa sample yang harus cocok: min(MIN_SAMPLES_MUST_MATCH, totalSamples)
        $minRequired = min(self::MIN_SAMPLES_MUST_MATCH, $totalSamples);
        $consensusOk = $matchedCount >= $minRequired;

        return [
            'match' => $consensusOk && $maxSimilarity >= self::COSINE_THRESHOLD,
            'similarity' => $maxSimilarity,
            'threshold' => self::COSINE_THRESHOLD,
            'samples_checked' => $totalSamples,
            'samples_matched' => $matchedCount,
            'min_required' => $minRequired,
        ];
    }
}
