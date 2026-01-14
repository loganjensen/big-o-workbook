<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';

interface QuizOption {
    id: string;
    text: string;
    isCorrect: boolean;
}

interface QuizQuestion {
    id: string;
    code: string;
    language: string;
    question: string;
    options: QuizOption[];
    explanation: string;
    difficulty: string;
}

interface Quiz {
    slug: string;
    questions: QuizQuestion[];
    generatedAt: string;
}

interface Props {
    slug: string;
}

const props = defineProps<Props>();

const quiz = ref<Quiz | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);
const currentQuestionIndex = ref(0);
const selectedAnswers = ref<Record<string, string>>({});
const submittedAnswers = ref<Record<string, boolean>>({});

const currentQuestion = computed(() => {
    if (!quiz.value || !quiz.value.questions[currentQuestionIndex.value]) {
        return null;
    }
    return quiz.value.questions[currentQuestionIndex.value];
});

const currentAnswer = computed(() => {
    if (!currentQuestion.value) return null;
    return selectedAnswers.value[currentQuestion.value.id];
});

const isSubmitted = computed(() => {
    if (!currentQuestion.value) return false;
    return !!submittedAnswers.value[currentQuestion.value.id];
});

const selectedOption = computed(() => {
    if (!currentQuestion.value || !currentAnswer.value) return null;
    return currentQuestion.value.options.find(
        (opt) => opt.id === currentAnswer.value
    );
});

const isCorrect = computed(() => {
    return selectedOption.value?.isCorrect ?? false;
});

const progress = computed(() => {
    if (!quiz.value) return 0;
    return Math.round(
        (Object.keys(submittedAnswers.value).length /
            quiz.value.questions.length) *
            100
    );
});

async function loadQuiz() {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get(`/api/big-o/${props.slug}/quiz`);
        quiz.value = response.data;
    } catch (e: any) {
        error.value =
            e.response?.data?.error ||
            'Failed to load quiz. Please try again.';
    } finally {
        loading.value = false;
    }
}

async function regenerateQuiz() {
    loading.value = true;
    error.value = null;
    currentQuestionIndex.value = 0;
    selectedAnswers.value = {};
    submittedAnswers.value = {};

    try {
        const response = await axios.post(
            `/api/big-o/${props.slug}/quiz/regenerate`
        );
        quiz.value = response.data;
    } catch (e: any) {
        error.value =
            e.response?.data?.error ||
            'Failed to regenerate quiz. Please try again.';
    } finally {
        loading.value = false;
    }
}

function selectAnswer(optionId: string) {
    if (!currentQuestion.value || isSubmitted.value) return;
    selectedAnswers.value[currentQuestion.value.id] = optionId;
}

function submitAnswer() {
    if (!currentQuestion.value || !currentAnswer.value) return;
    submittedAnswers.value[currentQuestion.value.id] = true;
}

function nextQuestion() {
    if (!quiz.value || currentQuestionIndex.value >= quiz.value.questions.length - 1)
        return;
    currentQuestionIndex.value++;
}

function previousQuestion() {
    if (currentQuestionIndex.value <= 0) return;
    currentQuestionIndex.value--;
}

onMounted(() => {
    loadQuiz();
});
</script>

<template>
    <div class="space-y-6">
        <!-- Loading State -->
        <div v-if="loading" class="space-y-4">
            <Skeleton class="h-48 w-full" />
            <Skeleton class="h-8 w-3/4" />
            <div class="space-y-2">
                <Skeleton class="h-10 w-full" />
                <Skeleton class="h-10 w-full" />
                <Skeleton class="h-10 w-full" />
                <Skeleton class="h-10 w-full" />
            </div>
            <Skeleton class="h-10 w-32" />
        </div>

        <!-- Error State -->
        <Alert v-else-if="error" variant="destructive">
            <AlertTitle>Error Loading Quiz</AlertTitle>
            <AlertDescription>
                <p>{{ error }}</p>
                <Button
                    @click="loadQuiz"
                    variant="outline"
                    size="sm"
                    class="mt-3"
                >
                    Try Again
                </Button>
            </AlertDescription>
        </Alert>

        <!-- Quiz Content -->
        <div v-else-if="quiz && currentQuestion" class="space-y-6">
            <!-- Header with Progress and Regenerate Button -->
            <div
                class="flex items-center justify-between border-b border-border pb-4"
            >
                <div class="space-y-1">
                    <p class="text-sm font-medium text-foreground">
                        Question {{ currentQuestionIndex + 1 }} of
                        {{ quiz.questions.length }}
                    </p>
                    <div class="h-2 w-48 overflow-hidden rounded-full bg-muted">
                        <div
                            class="h-full bg-primary transition-all duration-300"
                            :style="{ width: `${progress}%` }"
                        ></div>
                    </div>
                </div>
                <Button
                    @click="regenerateQuiz"
                    variant="outline"
                    size="sm"
                    :disabled="loading"
                >
                    Generate New Quiz
                </Button>
            </div>

            <!-- Code Block -->
            <div
                class="overflow-x-auto rounded-lg border border-border bg-muted/50 p-4"
            >
                <div class="mb-2 flex items-center justify-between">
                    <span
                        class="text-xs font-medium uppercase text-muted-foreground"
                    >
                        {{ currentQuestion.language }}
                    </span>
                    <span
                        class="rounded-full px-2 py-1 text-xs font-medium"
                        :class="{
                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400':
                                currentQuestion.difficulty === 'easy',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400':
                                currentQuestion.difficulty === 'medium',
                            'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400':
                                currentQuestion.difficulty === 'hard',
                        }"
                    >
                        {{ currentQuestion.difficulty }}
                    </span>
                </div>
                <pre
                    class="text-sm leading-relaxed text-foreground font-mono"
                ><code>{{ currentQuestion.code }}</code></pre>
            </div>

            <!-- Question -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-foreground">
                    {{ currentQuestion.question }}
                </h3>

                <!-- Options -->
                <div class="space-y-3">
                    <label
                        v-for="option in currentQuestion.options"
                        :key="option.id"
                        class="block cursor-pointer rounded-lg border border-border bg-card p-4 transition-all hover:border-primary hover:bg-accent/50"
                        :class="{
                            'border-primary bg-accent/50':
                                currentAnswer === option.id && !isSubmitted,
                            'border-green-500 bg-green-50 dark:bg-green-900/20':
                                isSubmitted && option.isCorrect,
                            'border-red-500 bg-red-50 dark:bg-red-900/20':
                                isSubmitted &&
                                currentAnswer === option.id &&
                                !option.isCorrect,
                        }"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="radio"
                                :name="`question-${currentQuestion.id}`"
                                :value="option.id"
                                :checked="currentAnswer === option.id"
                                :disabled="isSubmitted"
                                @change="selectAnswer(option.id)"
                                class="h-4 w-4 border-gray-300 text-primary focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            />
                            <span
                                class="text-sm font-medium text-foreground"
                                :class="{
                                    'text-green-700 dark:text-green-400':
                                        isSubmitted && option.isCorrect,
                                    'text-red-700 dark:text-red-400':
                                        isSubmitted &&
                                        currentAnswer === option.id &&
                                        !option.isCorrect,
                                }"
                            >
                                {{ option.text }}
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Explanation (shown after submission) -->
            <Alert
                v-if="isSubmitted"
                :variant="isCorrect ? 'default' : 'destructive'"
                class="border-l-4"
                :class="{
                    'border-l-green-500 bg-green-50 dark:bg-green-900/20':
                        isCorrect,
                    'border-l-red-500': !isCorrect,
                }"
            >
                <AlertTitle
                    :class="{
                        'text-green-700 dark:text-green-400': isCorrect,
                        'text-red-700 dark:text-red-400': !isCorrect,
                    }"
                >
                    {{ isCorrect ? '✓ Correct!' : '✗ Incorrect' }}
                </AlertTitle>
                <AlertDescription>
                    <p class="mt-2 text-sm leading-relaxed">
                        {{ currentQuestion.explanation }}
                    </p>
                </AlertDescription>
            </Alert>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4">
                <Button
                    @click="previousQuestion"
                    variant="outline"
                    :disabled="currentQuestionIndex === 0"
                >
                    Previous
                </Button>

                <Button
                    v-if="!isSubmitted"
                    @click="submitAnswer"
                    :disabled="!currentAnswer"
                >
                    Submit Answer
                </Button>

                <Button
                    v-else-if="
                        currentQuestionIndex < quiz.questions.length - 1
                    "
                    @click="nextQuestion"
                >
                    Next Question
                </Button>

                <div
                    v-else
                    class="flex items-center gap-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-5 w-5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Quiz Complete!
                </div>
            </div>
        </div>
    </div>
</template>
