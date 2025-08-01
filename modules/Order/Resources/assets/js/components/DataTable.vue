<template>
    <div v-loading="loading_submit">
        <div class="row ">

            <div class="col-md-9 col-lg-9 col-xl-9 filter-container">
                <div class="btn-filter-content">
                    <el-button
                        type="primary"
                        class="btn-show-filter mb-2"
                        :class="{ shift: isVisible }"
                        @click="toggleInformation"
                    >
                        {{ isVisible ? "Ocultar filtros" : "Mostrar filtros" }}
                    </el-button>
                </div>
                <div class="row" v-if="applyFilter && isVisible">
                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2">
                        <label for="">Tipo</label>
                        <el-select v-model="search.column"  placeholder="Select" @change="changeClearInput">
                            <el-option v-for="(label, key) in columns" :key="key" :value="key" :label="label"></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <template v-if="search.column=='date_of_issue' || search.column=='date_of_due' || search.column=='date_of_payment' || search.column=='delivery_date'">
                            <label for="">Fecha</label>
                            <el-date-picker
                                v-model="search.value"
                                type="date"
                                style="width: 100%;"
                                placeholder="Buscar"
                                value-format="yyyy-MM-dd"
                                @change="getRecords">
                            </el-date-picker>
                        </template>
                        <template v-else>
                            <label for="">Nombre</label>
                            <el-input placeholder="Buscar"
                                v-model="search.value"
                                style="width: 100%;"
                                prefix-icon="el-icon-search"
                                @input="getRecords">
                            </el-input>
                        </template>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2" v-if="config.order_node_advanced">
                        <label for="status">Estado</label>
                        <el-select v-model="search.state"  placeholder="Select" @change="getRecords">
                            <el-option v-for="(label, key) in state_types" :key="key" :value="label.id" :label="label.description"></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2" v-if="config.order_node_advanced">
                        <label for="status">Pago</label>
                        <el-select v-model="search.state_payment"  placeholder="Select" @change="getRecords">
                            <el-option v-for="(label, key) in state_payments" :key="key" :value="label.id" :label="label.description"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2 btn-container-mobile">
                        <el-button type="primary" v-if="typeUser == 'admin' &&  soapCompany != '03'" @click.prevent="clickGenerateDocuments()" >
                            Generar comprobantes
                        </el-button>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>

                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <slot name="heading"></slot>
                        </thead>
                        <tbody>
                        <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                        </tbody>
                    </table>
                    <div>
                        <el-pagination
                                @current-change="getRecords"
                                layout="total, prev, pager, next"
                                :total="pagination.total"
                                :current-page.sync="pagination.current_page"
                                :page-size="pagination.per_page">
                        </el-pagination>
                    </div>
                </div>
            </div>
        </div>

        <generate-documents :showDialog.sync="showDialogDocuments"></generate-documents>
    </div>
</template>


<script>

    import moment from 'moment'
    import queryString from 'query-string'
    import GenerateDocuments from './partials/documents.vue'
    import {mapActions, mapState} from "vuex"

    export default {
        components: {GenerateDocuments},
        props: {
            resource: String,
            typeUser: String,
            soapCompany: String,
            applyFilter:{
                type: Boolean,
                default: true,
                required: false
            }
        },
        data () {
            return {
                search: {
                    column: null,
                    value: null
                },
                columns: [],
                records: [],
                pagination: {},
                isVisible: false,
                showDialogDocuments: false,
                loading_submit: false,
                state_types: [
                    {id: '', description: 'Todos'},
                    {id:'01', description: 'Pendiente', class: 'text-warning'},
                    {id:'03', description: 'Por Entregar', class: 'text-info'},
                    {id:'05', description: 'Entregado', class: 'text-success'},
                    {id:'11', description: 'Anulado', class: 'text-danger'}
                ],
                state_payments: [
                    {id:'', description: 'Todos'},
                    {id:'0', description: 'Pendiente'},
                    {id:'1', description: 'Completado'}
                ],
                showLeftShadow: false,
                showRightShadow: false,
            }
        },
        computed:{
            ...mapState([
                'config',
            ]),
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
            this.$store.commit('setConfiguration', this.configuration)
            this.loadConfiguration()
        },
        async mounted () {

            let column_resource = _.split(this.resource, '/')
           // console.log(column_resource)
            await this.$http.get(`/${_.head(column_resource)}/columns`).then((response) => {
                this.columns = response.data
                this.search.column = _.head(Object.keys(this.columns))
            });
            await this.getRecords()

            this.$nextTick(() => {
                const el = this.$refs.scrollContainer;
                if (el) {
                    el.addEventListener('scroll', this.checkScrollShadows);
                    this.checkScrollShadows();
                }
            });
        },
        methods: {
            checkScrollShadows() {
                const el = this.$refs.scrollContainer;
                if (!el) return;

                const scrollLeft = el.scrollLeft;
                const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;

                const threshold = 2;

                this.showLeftShadow = scrollLeft > threshold;
                this.showRightShadow = scrollRight > threshold;
            },
            toggleInformation() {
                this.isVisible = !this.isVisible;
            },
            ...mapActions([
                'loadConfiguration',
            ]),
            clickGenerateDocuments(){
                this.showDialogDocuments = true
            },
            customIndex(index) {
                return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
            },
            getRecords() {
                this.loading_submit = true
                return this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                }).catch(error => {
                }).then(() => {
                    this.loading_submit = false
                });
            },
            getQueryParameters() {
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.search
                })
            },
            changeClearInput(){
                this.search.value = ''
                this.getRecords()
            }
        }
    }
</script>
