<?php

test('validates quiz fixture structure', function () {
    $fixtureContent = file_get_contents(__DIR__.'/../../Fixtures/quiz-responses/o-n-quiz.json');
    $quiz = json_decode($fixtureContent, true);

    expect($quiz)->toBeArray()
        ->and($quiz)->toHaveKey('questions')
        ->and($quiz['questions'])->toHaveCount(5);

    foreach ($quiz['questions'] as $question) {
        expect($question)->toHaveKeys([
            'id',
            'code',
            'language',
            'question',
            'options',
            'explanation',
            'difficulty',
        ])
            ->and($question['options'])->toHaveCount(4);

        $correctCount = 0;
        foreach ($question['options'] as $option) {
            expect($option)->toHaveKeys(['id', 'text', 'isCorrect']);
            if ($option['isCorrect']) {
                $correctCount++;
            }
        }

        expect($correctCount)->toBe(1);
    }
});
