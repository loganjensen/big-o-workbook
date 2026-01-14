<?php

use App\Services\BigOQuizService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('quiz endpoint returns valid quiz structure', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('getCachedOrGenerate')
            ->with('o-n')
            ->once()
            ->andReturn($mockQuiz);
    });

    $response = $this->get('/api/big-o/o-n/quiz');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'slug',
            'questions' => [
                '*' => [
                    'id',
                    'code',
                    'language',
                    'question',
                    'options' => [
                        '*' => ['id', 'text', 'isCorrect'],
                    ],
                    'explanation',
                    'difficulty',
                ],
            ],
            'generatedAt',
        ])
        ->assertJson([
            'slug' => 'o-n',
        ]);
});

test('regenerate endpoint clears cache and returns new quiz', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('regenerate')
            ->with('o-n')
            ->once()
            ->andReturn($mockQuiz);
    });

    $response = $this->post('/api/big-o/o-n/quiz/regenerate');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'slug',
            'questions',
            'generatedAt',
        ]);
});

test('quiz endpoint returns 404 for invalid slug', function () {
    $response = $this->get('/api/big-o/invalid-slug/quiz');

    $response->assertNotFound()
        ->assertJson([
            'error' => 'Invalid Big-O complexity slug',
        ]);
});

test('regenerate endpoint returns 404 for invalid slug', function () {
    $response = $this->post('/api/big-o/invalid-slug/quiz/regenerate');

    $response->assertNotFound()
        ->assertJson([
            'error' => 'Invalid Big-O complexity slug',
        ]);
});

test('quiz endpoint handles service failures gracefully', function () {
    $this->mock(BigOQuizService::class, function ($mock) {
        $mock->shouldReceive('getCachedOrGenerate')
            ->with('o-n')
            ->once()
            ->andThrow(new Exception('Failed to generate quiz. Please try again.'));
    });

    $response = $this->get('/api/big-o/o-n/quiz');

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

test('quiz returns correct number of questions', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('getCachedOrGenerate')
            ->with('o-n')
            ->once()
            ->andReturn($mockQuiz);
    });

    $response = $this->get('/api/big-o/o-n/quiz');

    $response->assertSuccessful();

    $questions = $response->json('questions');
    expect($questions)->toHaveCount(5);
});

test('quiz questions have exactly 4 options', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('getCachedOrGenerate')
            ->with('o-n')
            ->once()
            ->andReturn($mockQuiz);
    });

    $response = $this->get('/api/big-o/o-n/quiz');

    $response->assertSuccessful();

    $questions = $response->json('questions');
    foreach ($questions as $question) {
        expect($question['options'])->toHaveCount(4);
    }
});

test('quiz works for all complexity types', function (string $slug) {
    $mockQuiz = [
        'slug' => $slug,
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz, $slug) {
        $mock->shouldReceive('getCachedOrGenerate')
            ->with($slug)
            ->once()
            ->andReturn($mockQuiz);
    });

    $response = $this->get("/api/big-o/{$slug}/quiz");

    $response->assertSuccessful()
        ->assertJson([
            'slug' => $slug,
        ]);
})->with([
    'o-1',
    'o-log-n',
    'o-n',
    'o-n-log-n',
    'o-n-squared',
    'o-2-n',
    'o-n-factorial',
]);
