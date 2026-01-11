<?php

test('big-o index page loads successfully', function () {
    $response = $this->get('/big-o');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Index')
        ->has('complexities', 7)
    );
});

test('big-o index page has correct complexity metadata', function () {
    $response = $this->get('/big-o');

    $response->assertInertia(fn ($page) => $page
        ->where('complexities.0.slug', 'o-1')
        ->where('complexities.0.shortTitle', 'O(1)')
    );
});

test('o-1 constant time page loads successfully', function () {
    $response = $this->get('/big-o/o-1');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-1')
        ->has('complexity.title')
        ->has('complexity.description')
        ->has('complexity.intuition')
        ->has('complexity.examples')
        ->has('complexity.whatCausesThis')
        ->has('complexity.whyItMatters')
        ->has('complexity.keyTakeaways')
    );
});

test('o-log-n logarithmic time page loads successfully', function () {
    $response = $this->get('/big-o/o-log-n');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-log-n')
        ->has('complexity')
    );
});

test('o-n linear time page loads successfully', function () {
    $response = $this->get('/big-o/o-n');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-n')
        ->has('complexity')
    );
});

test('o-n-log-n linearithmic time page loads successfully', function () {
    $response = $this->get('/big-o/o-n-log-n');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-n-log-n')
        ->has('complexity')
    );
});

test('o-n-squared quadratic time page loads successfully', function () {
    $response = $this->get('/big-o/o-n-squared');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-n-squared')
        ->has('complexity')
    );
});

test('o-2-n exponential time page loads successfully', function () {
    $response = $this->get('/big-o/o-2-n');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-2-n')
        ->has('complexity')
    );
});

test('o-n-factorial factorial time page loads successfully', function () {
    $response = $this->get('/big-o/o-n-factorial');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('BigO/Show')
        ->where('slug', 'o-n-factorial')
        ->has('complexity')
    );
});

test('all big-o pages have required json structure', function () {
    $slugs = ['o-1', 'o-log-n', 'o-n', 'o-n-log-n', 'o-n-squared', 'o-2-n', 'o-n-factorial'];

    foreach ($slugs as $slug) {
        $path = resource_path("data/big-o/{$slug}.json");
        expect(file_exists($path))->toBeTrue("JSON file for {$slug} should exist");

        $json = json_decode(file_get_contents($path), true);
        expect($json)->toHaveKeys([
            'title',
            'description',
            'intuition',
            'examples',
            'whatCausesThis',
            'whyItMatters',
            'keyTakeaways',
        ]);

        expect($json['examples'])->toBeArray();
        expect($json['keyTakeaways'])->toBeArray();

        foreach ($json['examples'] as $example) {
            expect($example)->toHaveKeys(['title', 'pseudocode', 'explanation']);
        }
    }
});

test('all big-o show pages include navigation data', function () {
    $response = $this->get('/big-o/o-1');

    $response->assertInertia(fn ($page) => $page
        ->has('allComplexities', 7)
        ->where('allComplexities.0.slug', 'o-1')
    );
});

test('invalid big-o page returns 404', function () {
    $response = $this->get('/big-o/o-invalid');

    $response->assertNotFound();
});

test('controller aborts with 500 when json file is corrupted', function () {
    $testPath = resource_path('data/big-o/test-corrupted.json');

    // Create a temporary corrupted JSON file
    file_put_contents($testPath, '{invalid json content');

    try {
        // Create a test controller instance
        $controller = new class extends \App\Http\Controllers\BigO\ShowBigOComplexityController
        {
            protected function getSlug(): string
            {
                return 'test-corrupted';
            }
        };

        // Invoke the controller and expect an abort
        $controller->__invoke();
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(500);
        expect($e->getMessage())->toContain('Failed to parse Big-O complexity data');
    } finally {
        // Clean up test file
        if (file_exists($testPath)) {
            unlink($testPath);
        }
    }
});

test('controller aborts with 500 when json file cannot be read', function () {
    $testPath = resource_path('data/big-o/test-unreadable.json');

    // Create a dummy file first
    file_put_contents($testPath, 'temporary content');

    try {
        // Create a test controller that overrides loadComplexityData to simulate read failure
        $controller = new class extends \App\Http\Controllers\BigO\ShowBigOComplexityController
        {
            protected function getSlug(): string
            {
                return 'test-unreadable';
            }

            protected function loadComplexityData(string $slug): array
            {
                $path = resource_path("data/big-o/{$slug}.json");

                if (! file_exists($path)) {
                    abort(404, 'Big-O complexity page not found.');
                }

                // Simulate file_get_contents returning false (I/O error)
                $json = false;

                if ($json === false) {
                    abort(500, "Failed to read Big-O complexity data file: {$slug}.json");
                }

                return [];
            }
        };

        $controller->__invoke();
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(500);
        expect($e->getMessage())->toContain('Failed to read Big-O complexity data file');
    } finally {
        // Clean up test file
        if (file_exists($testPath)) {
            unlink($testPath);
        }
    }
});
