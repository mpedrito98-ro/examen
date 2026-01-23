<template>
    <v-container fluid>


        <!-- COLORES -->
        <v-card class="mt-5">
            <v-toolbar color="success" density="compact">
                <v-toolbar-title>Roles</v-toolbar-title>
                <v-spacer/>
                <v-toolbar-items>
                    <v-btn @click="createColor" v-if="can('Create Color')" variant="text">
                        <v-icon>mdi-plus</v-icon>
                        Add
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-card-text class="py-2">
                <v-table>
                    <thead>
                    <tr>
                        <td>Color</td>
                        <td>Base</td>
                        <td>Action</td>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="collection.colores!==null" v-for="(item, index) of collection.colores.data">
                        <tr>
                            <td v-text="item.color"/>
                            <td v-text="item.base"/>
                            <td>
                                <v-btn
                                    @click="editColor(item)"
                                    v-if="can('Update Color')" variant="text">
                                    <v-icon>mdi-square-edit-outline</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </v-table>
            </v-card-text>
        </v-card>


        <!-- Dialog Create/Edit Color -->
        <v-dialog width="500" v-model="dialog.color.create">
            <v-card>
                <v-toolbar density="compact" color="success">
                    <v-toolbar-title v-text="field.color.id===null?'Add Color':'Edit Color'"/>
                    <v-spacer/>
                    <v-toolbar-items>
                        <v-btn @click="dialog.color.create=false" variant="text">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text class="py-2">
                    <v-text-field variant="outlined" density="compact" label="Color" v-model="field.color.color"/>
                    <v-text-field variant="outlined" density="compact" label="Base" v-model="field.color.base"/>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="dialog.color.delete=true" color="warning" v-if="field.color.id!==null">Delete</v-btn>
                    <v-spacer/>
                    <v-btn :loading="loading.color.update" :disabled="loading.color.update" @click="updateColor"
                           variant="flat" color="success" v-if="field.color.id!==null">Update
                    </v-btn>
                    <v-btn :loading="loading.color.create" :disabled="loading.color.create" @click="storeColor"
                           variant="flat" color="success" v-if="field.color.id===null">Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog delete COLOR confirmation -->
        <v-dialog width="350" v-model="dialog.color.delete">
            <v-card>
                <v-toolbar density="compact" color="warning">
                    <v-toolbar-title>Confirmation</v-toolbar-title>
                    <v-spacer/>
                    <v-toolbar-items>
                        <v-btn @click="dialog.color.delete=false" variant="text">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text class="py-2">
                    <p>Do you want to delete this selected user?</p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer/>

                    <v-btn :loading="loading.color.delete" :disabled="loading.color.delete" @click="deleteColor"
                           variant="flat" color="warning" v-if="field.color.id!==null">Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>


    </v-container>
    <lg-toast ref="message"/>
</template>

<script>
import axios from "axios";
import LgToast from "../../components/helpers/Toast.vue";
import Container from "../../components/helpers/Container.vue";
import { mapGetters} from 'vuex';
export default {
    name: "Color",
    components: {Container, LgToast},
    data() {
        return {
            collection: {
                colores: null,
            },
            field: {
                color: {
                    id: null,
                    color: null,
                    base: null
                }
            },
            dialog: {
                color: {
                    create: false,
                    delete: false
                }
            },
            loading: {
                color: {
                    create: false,
                    delete: false,
                    update: false,
                }
            },
        }
    },
    methods: {
        getColors() {
            axios.get('/api/v1/colores').then(res => {
                console.log('Colors data:', res.data); // Debugging
                this.collection.colores = res.data.data
            })
        },
        storeColor() {
            this.loading.color.create = true
            axios.post('/api/v1/colores', this.field.color).then(res => {
                if (res.data.code === 200) {
                    this.$refs.message.show(res.data.message)
                    this.field.color = {
                        id: null,
                        color: null,
                        base: null,
                    }
                    this.dialog.color.create = false
                    this.getColors()
                } else {
                    this.$refs.message.show(res.data.message, 'warning')
                }
            }).finally(() => {
                this.loading.color.create = false
            })
        },

        updateColor() {
            this.loading.color.update = true
            axios.patch('/api/v1/colores/' + this.field.color.id, this.field.color).then(res => {
                if (res.data.code === 200) {
                    this.$refs.message.show(res.data.message)
                    this.field.color = {
                        id: null,
                        color: null,
                        base: null,
                    }
                    this.dialog.color.create = false
                    this.getColors()
                } else {
                    this.$refs.message.show(res.data.message, 'warning')
                }
            }).finally(() => {
                this.loading.color.update = false
            })
        },

        editColor(color) {
            this.field.color.id = color.id
            this.field.color.color = color.color
            this.field.color.base = color.base
            this.dialog.color.create = true
        },

        deleteColor() {
            this.loading.color.delete = true
            axios.delete('/api/v1/colores/' + this.field.color.id).then(res => {
                if (res.data.code === 200) {
                    this.$refs.message.show(res.data.message)
                    this.dialog.color.delete = false
                    this.dialog.color.create = false
                    this.getColors()
                } else {
                    this.$refs.message.show(res.data.message, 'warning')
                }
            }).finally(() => {
                this.loading.color.delete = false
            })
        },

        createColor() {
            this.field.color = {
                id: null,
                name: null,
                base: null,
            }
            this.dialog.color.create = true
        },

    },
    mounted() {
        this.getColors()
    },
    computed:{
        ...mapGetters(['user']),
    }
}
</script>

<style scoped>

</style>









