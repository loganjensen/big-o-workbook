<?php

namespace App\Services;

use Prism\Prism\Facades\Prism;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BigOQuizService
{
    /**
     * Generate a new quiz for the given Big-O complexity slug.
     *
     * @param  string  $slug  The Big-O complexity slug (e.g., 'o-n', 'o-log-n')
     * @param  int  $questionCount  Number of questions to generate
     * @return array The generated quiz data
     *
     * @throws \Exception If quiz generation or validation fails
     */
    public function generateQuiz(string $slug, int $questionCount = 5): array
    {
        $complexityData = $this->loadComplexityData($slug);

        $prompt = $this->buildPrompt($complexityData, $questionCount);

        try {
            $response = Prism::text()
                ->using(
                    config('big-o.quiz.anthropic_model', 'claude-3-5-sonnet-20241022'),
                    'anthropic'
                )
                ->withMaxTokens(config('big-o.quiz.max_tokens', 4000))
                ->withTemperature(config('big-o.quiz.temperature', 0.7))
                ->withPrompt($prompt)
                ->generate();

            $content = $response->text;

            // Extract JSON from the response (handle markdown code blocks)
            $content = $this->extractJson($content);

            $quiz = json_decode($content, true);

            if (! $this->validateQuizStructure($quiz)) {
                throw new \Exception('Generated quiz has invalid structure');
            }

            return [
                'slug' => $slug,
                'questions' => $quiz['questions'],
                'generatedAt' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate Big-O quiz', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to generate quiz. Please try again.');
        }
    }

    /**
     * Get cached quiz or generate a new one.
     *
     * @param  string  $slug  The Big-O complexity slug
     * @return array The quiz data
     */
    public function getCachedOrGenerate(string $slug): array
    {
        $cacheKey = $this->getCacheKey($slug);
        $cacheTtl = config('big-o.quiz.cache_ttl', 604800); // 7 days default

        return Cache::remember($cacheKey, $cacheTtl, function () use ($slug) {
            return $this->generateQuiz(
                $slug,
                config('big-o.quiz.question_count', 5)
            );
        });
    }

    /**
     * Regenerate quiz by clearing cache and generating new one.
     *
     * @param  string  $slug  The Big-O complexity slug
     * @return array The newly generated quiz data
     */
    public function regenerate(string $slug): array
    {
        $this->clearCache($slug);

        return $this->generateQuiz(
            $slug,
            config('big-o.quiz.question_count', 5)
        );
    }

    /**
     * Clear the cached quiz for the given slug.
     *
     * @param  string  $slug  The Big-O complexity slug
     */
    public function clearCache(string $slug): void
    {
        $cacheKey = $this->getCacheKey($slug);
        Cache::forget($cacheKey);
    }

    /**
     * Build the prompt for Claude to generate quiz questions.
     *
     * @param  array  $complexityData  The complexity data from JSON file
     * @param  int  $questionCount  Number of questions to generate
     * @return string The prompt for Claude
     */
    private function buildPrompt(array $complexityData, int $questionCount): string
    {
        $examplesJson = json_encode($complexityData['examples'], JSON_PRETTY_PRINT);
        $intuition = $complexityData['intuition'];
        $description = $complexityData['description'];
        $title = $complexityData['title'];

        return <<<PROMPT
You are an expert computer science educator creating quiz questions about Big-O time complexity.

Generate {$questionCount} multiple-choice code analysis questions for {$title}.

Context:
{$description}

Intuition:
{$intuition}

Example Code Patterns:
{$examplesJson}

Requirements:
1. Each question MUST present a code snippet in JavaScript, Python, or PHP
2. Ask "What is the time complexity of this code?"
3. Provide EXACTLY 4 multiple choice options with different time complexities
4. Only ONE option should be correct (isCorrect: true), the rest must be false
5. Include a detailed explanation that references the specific complexity characteristics
6. Vary the difficulty: 2 easy, 2 medium, 1 hard (or distribute evenly for {$questionCount} questions)
7. Use different programming languages across questions
8. Code should be realistic, practical, and clearly demonstrable
9. Each question must have a unique ID (use uuidv4 format)
10. Each option must have a unique ID (opt-1, opt-2, opt-3, opt-4)

CRITICAL: You MUST respond with ONLY valid JSON in this EXACT format (no markdown, no explanation, just JSON):

{
  "questions": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "code": "function findMax(arr) {\n  let max = arr[0];\n  for (let i = 1; i < arr.length; i++) {\n    if (arr[i] > max) max = arr[i];\n  }\n  return max;\n}",
      "language": "javascript",
      "question": "What is the time complexity of this code?",
      "options": [
        {"id": "opt-1", "text": "O(n)", "isCorrect": true},
        {"id": "opt-2", "text": "O(1)", "isCorrect": false},
        {"id": "opt-3", "text": "O(log n)", "isCorrect": false},
        {"id": "opt-4", "text": "O(n²)", "isCorrect": false}
      ],
      "explanation": "This code iterates through the array exactly once to find the maximum value. Since we examine each element once, the time complexity is O(n), where n is the length of the array.",
      "difficulty": "easy"
    }
  ]
}

Generate the quiz now in valid JSON format:
PROMPT;
    }

    /**
     * Extract JSON from response (handles markdown code blocks).
     *
     * @param  string  $content  The response content
     * @return string The extracted JSON string
     */
    private function extractJson(string $content): string
    {
        // Try to extract JSON from markdown code blocks
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            return trim($matches[1]);
        }

        // Return as-is if no code blocks found
        return trim($content);
    }

    /**
     * Validate the structure of the generated quiz.
     *
     * @param  mixed  $quiz  The quiz data to validate
     * @return bool True if valid, false otherwise
     */
    private function validateQuizStructure(mixed $quiz): bool
    {
        if (! is_array($quiz) || ! isset($quiz['questions']) || ! is_array($quiz['questions'])) {
            return false;
        }

        foreach ($quiz['questions'] as $question) {
            // Check required fields
            if (! isset($question['id'], $question['code'], $question['language'],
                $question['question'], $question['options'], $question['explanation'],
                $question['difficulty'])) {
                return false;
            }

            // Validate options
            if (! is_array($question['options']) || count($question['options']) !== 4) {
                return false;
            }

            // Check that exactly one option is correct
            $correctCount = 0;
            foreach ($question['options'] as $option) {
                if (! isset($option['id'], $option['text'], $option['isCorrect'])) {
                    return false;
                }

                if ($option['isCorrect'] === true) {
                    $correctCount++;
                }
            }

            if ($correctCount !== 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Load complexity data from JSON file.
     *
     * @param  string  $slug  The complexity slug
     * @return array The complexity data
     *
     * @throws \Exception If file not found or invalid
     */
    private function loadComplexityData(string $slug): array
    {
        $path = resource_path("data/big-o/{$slug}.json");

        if (! file_exists($path)) {
            throw new \Exception("Big-O complexity data not found for slug: {$slug}");
        }

        $json = file_get_contents($path);

        if ($json === false) {
            throw new \Exception("Failed to read Big-O complexity data file: {$slug}.json");
        }

        $data = json_decode($json, true);

        if ($data === null || json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse Big-O complexity data: '.json_last_error_msg());
        }

        return $data;
    }

    /**
     * Get the cache key for the given slug.
     *
     * @param  string  $slug  The complexity slug
     * @return string The cache key
     */
    private function getCacheKey(string $slug): string
    {
        $version = config('big-o.quiz.cache_version', 1);

        return "big-o-quiz:{$slug}:v{$version}";
    }
}
