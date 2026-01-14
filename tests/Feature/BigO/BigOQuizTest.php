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
        ->assertJson([
            'error' => 'Internal server error',
        ]);
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

test('regenerate endpoint allows up to 10 requests per minute', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('regenerate')
            ->with('o-n')
            ->times(10)
            ->andReturn($mockQuiz);
    });

    for ($i = 0; $i < 10; $i++) {
        $response = $this->post('/api/big-o/o-n/quiz/regenerate');
        $response->assertSuccessful();
    }
});

test('regenerate endpoint returns 429 after rate limit exceeded', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('regenerate')
            ->with('o-n')
            ->times(10)
            ->andReturn($mockQuiz);
    });

    // Make 10 successful requests
    for ($i = 0; $i < 10; $i++) {
        $response = $this->post('/api/big-o/o-n/quiz/regenerate');
        $response->assertSuccessful();
    }

    // 11th request should be rate limited
    $response = $this->post('/api/big-o/o-n/quiz/regenerate');
    $response->assertStatus(429);
});

test('regenerate endpoint rate limiting is per IP address', function () {
    $mockQuiz = [
        'slug' => 'o-n',
        'questions' => json_decode(file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json'), true)['questions'],
        'generatedAt' => now()->toIso8601String(),
    ];

    $this->mock(BigOQuizService::class, function ($mock) use ($mockQuiz) {
        $mock->shouldReceive('regenerate')
            ->with('o-n')
            ->times(20)
            ->andReturn($mockQuiz);
    });

    // Make 10 requests from first IP
    for ($i = 0; $i < 10; $i++) {
        $response = $this->post('/api/big-o/o-n/quiz/regenerate', [], [
            'REMOTE_ADDR' => '192.168.1.1',
        ]);
        $response->assertSuccessful();
    }

    // Make 10 requests from second IP (should also succeed)
    for ($i = 0; $i < 10; $i++) {
        $response = $this->post('/api/big-o/o-n/quiz/regenerate', [], [
            'REMOTE_ADDR' => '192.168.1.2',
        ]);
        $response->assertSuccessful();
    }

    // 11th request from first IP should be rate limited
    $response = $this->post('/api/big-o/o-n/quiz/regenerate', [], [
        'REMOTE_ADDR' => '192.168.1.1',
    ]);
    $response->assertStatus(429);

    // But second IP can still make one more request
    $response = $this->post('/api/big-o/o-n/quiz/regenerate', [], [
        'REMOTE_ADDR' => '192.168.1.2',
    ]);
    $response->assertStatus(429);
});

test('regenerate endpoint handles service failures gracefully', function () {
    $this->mock(BigOQuizService::class, function ($mock) {
        $mock->shouldReceive('regenerate')
            ->with('o-n')
            ->once()
            ->andThrow(new Exception('Failed to regenerate quiz. Please try again.'));
    });

    $response = $this->post('/api/big-o/o-n/quiz/regenerate');

    $response->assertStatus(500)
        ->assertJson([
            'error' => 'Internal server error',
        ]);
});
