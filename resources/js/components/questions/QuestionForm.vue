<template>
    <v-card>
        <v-toolbar color="primary" density="compact">
            <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
            <v-spacer/>
            <v-toolbar-items>
                <v-btn @click="$emit('close')" variant="text">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
            <v-textarea
                label="Texto de la Pregunta"
                v-model="formData.question_text"
                required
                variant="outlined"
                density="compact"
            ></v-textarea>

            <v-divider class="my-4"></v-divider>
            <h3>Respuestas</h3>

            <div v-for="(answer, index) in formData.answers" :key="index" class="d-flex align-center my-2">
                <v-text-field
                    label="Texto de la Respuesta"
                    v-model="answer.answer_text"
                    required
                    variant="outlined"
                    density="compact"
                    class="mr-2"
                ></v-text-field>
                <v-checkbox
                    label="Correcta"
                    v-model="answer.is_correct"
                    @change="setCorrectAnswer(index)"
                ></v-checkbox>
                <v-btn icon @click="removeAnswer(index)" small color="error" variant="text">
                    <v-icon>mdi-delete</v-icon>
                </v-btn>
            </div>

            <v-btn @click="addAnswer" color="secondary" class="mt-2">
                <v-icon left>mdi-plus</v-icon>
                Agregar Respuesta
            </v-btn>

        </v-card-text>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="$emit('close')">Cancelar</v-btn>
            <v-btn color="primary" @click="save">Guardar</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import axios from 'axios';

export default {
    name: 'QuestionForm',
    props: {
        question: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            formData: {
                question_text: '',
                answers: [],
            },
        };
    },
    computed: {
        formTitle() {
            return this.question ? 'Editar Pregunta' : 'Crear Pregunta';
        },
    },
    watch: {
        question: {
            handler(newVal) {
                if (newVal) {
                    this.formData = {
                        id: newVal.id,
                        question_text: newVal.question_text,
                        answers: newVal.answers.map(a => ({...a})) // Deep copy
                    };
                } else {
                    this.resetForm();
                }
            },
            immediate: true,
        },
    },
    methods: {
        resetForm() {
            this.formData = {
                question_text: '',
                answers: [
                    { answer_text: '', is_correct: false },
                    { answer_text: '', is_correct: false },
                ],
            };
        },
        addAnswer() {
            this.formData.answers.push({ answer_text: '', is_correct: false });
        },
        removeAnswer(index) {
            if (this.formData.answers.length > 2) {
                this.formData.answers.splice(index, 1);
            } else {
                // Optionally show a message that at least 2 answers are required
            }
        },
        setCorrectAnswer(selectedIndex) {
            this.formData.answers.forEach((answer, index) => {
                answer.is_correct = (index === selectedIndex);
            });
        },
        save() {
            const url = this.question
                ? `/api/v1/questions/${this.question.id}`
                : '/api/v1/questions';
            const method = this.question ? 'put' : 'post';

            axios[method](url, this.formData)
                .then(() => {
                    this.$emit('saved');
                })
                .catch(error => {
                    console.error('Error saving question:', error);
                    // Optionally, show error messages to the user
                });
        },
    },
    created() {
        if (!this.question) {
            this.resetForm();
        }
    }
};
</script>
