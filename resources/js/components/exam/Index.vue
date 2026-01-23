<template>
    <v-container>
        <v-card v-if="!examFinished">
            <v-toolbar color="primary" density="compact">
                <v-toolbar-title>Examen</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-title>{{ formattedTimeLeft }}</v-toolbar-title>
            </v-toolbar>
            <v-card-text v-if="currentQuestion">
                <h2 class="mb-4">{{ currentQuestion.question_text }}</h2>
                <v-list>
                    <v-list-item
                        v-for="answer in currentQuestion.answers"
                        :key="answer.id"
                        @click="selectAnswer(answer)"
                        :class="getAnswerClass(answer)"
                        :disabled="answerSelected"
                    >
                        <v-list-item-title>{{ answer.answer_text }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="answerSelected"
                    @click="nextQuestion"
                    color="secondary"
                >
                    {{ isLastQuestion ? 'Finalizar' : 'Siguiente Pregunta' }}
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-card v-else>
            <v-toolbar color="success" density="compact">
                <v-toolbar-title>Examen Finalizado</v-toolbar-title>
            </v-toolbar>
            <v-card-text class="text-center">
                <h2 v-if="timeLeft === 0">¡Se acabó el tiempo!</h2>
                <h2>Puntuación Final: {{ score }} de {{ questions.length }}</h2>
                <v-btn @click="restartExam" color="primary" class="mt-4">Reiniciar Examen</v-btn>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Exam',
    data() {
        return {
            questions: [],
            currentQuestionIndex: 0,
            selectedAnswer: null,
            answerSelected: false,
            score: 0,
            examFinished: false,
            timer: null,
            timeLeft: 3600, // 60 minutes in seconds
        };
    },
    computed: {
        currentQuestion() {
            return this.questions[this.currentQuestionIndex];
        },
        isLastQuestion() {
            return this.currentQuestionIndex === this.questions.length - 1;
        },
        formattedTimeLeft() {
            const minutes = Math.floor(this.timeLeft / 60);
            const seconds = this.timeLeft % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
    },
    methods: {
        fetchQuestions() {
            axios.get('/api/v1/questions?paginate=false')
                .then(response => {
                    this.questions = response.data;
                    this.startTimer();
                });
        },
        startTimer() {
            this.timer = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    this.examFinished = true;
                    this.stopTimer();
                }
            }, 1000);
        },
        stopTimer() {
            clearInterval(this.timer);
        },
        selectAnswer(answer) {
            this.answerSelected = true;
            this.selectedAnswer = answer;
            if (answer.is_correct) {
                this.score++;
            }
        },
        getAnswerClass(answer) {
            if (!this.answerSelected) {
                return '';
            }
            if (answer.id === this.selectedAnswer.id) {
                return this.selectedAnswer.is_correct ? 'bg-success' : 'bg-error';
            }
            if (answer.is_correct) {
                return 'bg-success';
            }
            return '';
        },
        nextQuestion() {
            if (this.isLastQuestion) {
                this.examFinished = true;
                this.stopTimer();
            } else {
                this.currentQuestionIndex++;
                this.answerSelected = false;
                this.selectedAnswer = null;
            }
        },
        restartExam() {
            this.currentQuestionIndex = 0;
            this.score = 0;
            this.examFinished = false;
            this.answerSelected = false;
            this.selectedAnswer = null;
            this.timeLeft = 3600;
            this.startTimer();
        }
    },
    mounted() {
        this.fetchQuestions();
    },
    beforeUnmount() {
        this.stopTimer();
    }
};
</script>
<style scoped>
.v-list-item--disabled {
    opacity: 0.8;
}
</style>
