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
    
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button @click="clickFinalize">Ir al listado</el-button>
                <el-button type="primary" @click="clickNewDocument">Nueva compra</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog', 'recordId', 'showClose', 'type'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'fixed-asset/purchases',
                errors: {},
                form: {},
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    external_id: null,
                    number_full: null,
                    customer_email: null,
                    download_pdf: null
                }
            },
            create() {
                this.$http.get(`/${this.resource}/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data.data
                        let typei = this.type == 'edit' ? 'editada' : 'registrada'
                        this.titleDialog = `Compra ${typei}: ` +this.form.number_full
                    })
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
        }
    }
</script>