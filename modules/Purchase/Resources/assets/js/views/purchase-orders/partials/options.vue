<template>
    <el-dialog :visible="showDialog" @open="create" width="30%"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false">
        <span slot="title">
            <div class="widget-summary widget-summary-xs d-flex align-items-center">
                <div class="">
                    <div class="summary-icon bg-success succes-check-container m-0">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="widget-summary-col">
                    <div>
                        <div>
                            <span class="ml-2 el-dialog__title">{{ titleDialog }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </span>
        <span>Formatos disponibles para la descarga de la orden de compra:</span>
        <div class="row print-buttons-container">

            <template v-if="form.upload_filename">

                <div class="col text-center font-weight-bold mt-2">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light w-100" @click="clickPrint('a4')">
                        Imprimir A4
                    </button>
                </div>
                <div class="col text-center font-weight-bold mt-2">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light w-100" @click="clickDownload()">
                        Descargar Archivo
                    </button>
                </div>
            </template>
            <template v-else>

                <div class="col text-center font-weight-bold mt-2">
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light w-100" @click="clickPrint('a4')">
                        Imprimir A4
                    </button>
                </div>
            </template>


        </div>
        <span slot="footer" class="dialog-footer row">
            <div class="col-md-7">
                <el-input v-model="form.customer_email">
                    <el-button slot="append" icon="el-icon-message"   @click="clickSendEmail" :loading="loading">Enviar</el-button>
                </el-input>
            </div>
            <div class="col-md-5">
                <template v-if="showClose">
                    <el-button @click="clickClose">Cerrar</el-button>
                </template>
                <template v-else>
                    <el-button @click="clickFinalize">Ir al listado</el-button>
                    <el-button type="primary" @click="clickNewDocument">{{button_text}}</el-button>
                </template>
            </div>
        </span>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog', 'recordId', 'showClose', 'type','isUpdate'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'purchase-orders',
                button_text:'Nueva OC',
                errors: {},
                form: {},
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            clickPrint(format){
                window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
            },
            clickDownload(){
                window.open(`/${this.resource}/download-attached/${this.form.external_id}`, '_blank');
            },
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    external_id: null,
                    number: null,
                    customer_email: null,
                    upload_filename:null,
                    download_pdf: null
                }
            },
            create() {
                this.$http.get(`/${this.resource}/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data.data
                        let typei = this.type == 'edit' ? 'editada' : 'registrada'
                        this.titleDialog = `Orden de Compra ${typei}: ` + this.form.number_full
                    })
                this.button_text = this.isUpdate ? 'Continuar':'Nueva OC'
            },

            clickFinalize() {
                location.href = `/${this.resource}`
            },
            clickNewDocument() {
                this.clickClose()
            },
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },

            clickSendEmail() {
                this.loading=true
                this.$http.post(`/${this.resource}/email`, {
                    customer_email: this.form.customer_email,
                    id: this.form.id
                })
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success('El correo fue enviado satisfactoriamente')
                        } else {
                            this.$message.error('Error al enviar el correo')
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading=false

                    })
            },
        }
    }
</script>
