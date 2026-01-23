<template>
    <v-container fluid>
        <v-card>
            <v-toolbar color="primary" density="compact">
                <v-toolbar-title>Banco de Preguntas</v-toolbar-title>
                <v-spacer/>
                <v-toolbar-items>
                    <v-btn @click="createQuestion" variant="text">
                        <v-icon>mdi-plus</v-icon>
                        Agregar
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-card-text class="py-2">
                <v-table>
                    <thead>
                    <tr>
                        <th>Pregunta</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="question in questions.data" :key="question.id">
                        <td>{{ question.question_text }}</td>
                        <td>
                            <v-btn @click="editQuestion(question)" variant="text">
                                <v-icon>mdi-square-edit-outline</v-icon>
                            </v-btn>
                            <v-btn @click="confirmDelete(question)" variant="text" color="error">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-table>
            </v-card-text>
        </v-card>

        <!-- Form Dialog -->
        <v-dialog v-model="dialog.form" persistent max-width="800px">
            <question-form
                :question="selectedQuestion"
                @close="dialog.form = false"
                @saved="onQuestionSaved"
            ></question-form>
        </v-dialog>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="dialog.delete" max-width="400px">
            <v-card>
                <v-toolbar density="compact" color="warning">
                    <v-toolbar-title>Confirmar Eliminación</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    ¿Estás seguro de que quieres eliminar esta pregunta?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="dialog.delete = false">Cancelar</v-btn>
                    <v-btn color="warning" @click="deleteQuestion">Eliminar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <lg-toast ref="message"/>
    </v-container>
</template>

<script>
import axios from 'axios';
import QuestionForm from './QuestionForm.vue';
import LgToast from '../../../components/helpers/Toast.vue';

export default {
    name: 'QuestionIndex',
    components: { QuestionForm, LgToast },
    data() {
        return {
            questions: {
                data: []
            },
            dialog: {
                form: false,
                delete: false,
            },
            selectedQuestion: null,
            questionToDelete: null,
        };
    },
    methods: {
        fetchQuestions() {
            axios.get('/api/v1/questions').then(response => {
                this.questions = response.data;
            });
        },
        createQuestion() {
            this.selectedQuestion = null;
            this.dialog.form = true;
        },
        editQuestion(question) {
            this.selectedQuestion = Object.assign({}, question);
            this.dialog.form = true;
        },
        confirmDelete(question) {
            this.questionToDelete = question;
            this.dialog.delete = true;
        },
        deleteQuestion() {
            axios.delete(`/api/v1/questions/${this.questionToDelete.id}`).then(() => {
                this.$refs.message.show('Pregunta eliminada correctamente');
                this.fetchQuestions();
                this.dialog.delete = false;
            }).catch(error => {
                this.$refs.message.show(error.response.data.message || 'Error al eliminar la pregunta', 'warning');
            });
        },
        onQuestionSaved() {
            this.dialog.form = false;
            this.fetchQuestions();
            this.$refs.message.show('Pregunta guardada correctamente');
        }
    },
    mounted() {
        this.fetchQuestions();
    }
};
</script>
