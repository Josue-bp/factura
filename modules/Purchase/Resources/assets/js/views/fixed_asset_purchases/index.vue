<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/fixed-asset/purchases">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Compras - Activos fijos</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="primary">
                        Mostrar/Ocultar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="(column, index) in columns" :key="index">
                            <el-checkbox v-model="column.visible" @change="saveColumnVisibility">{{ column.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <!-- <th>#</th> -->
                        <th class="text-left">F. Emisión</th>
                        <th class="text-center" v-if="columns.date_of_due.visible" >F. Vencimiento</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <th>Número</th>
                        <th>Productos</th> 
                        <th class="text-center">Moneda</th>
                        <th v-if="columns.total_free.visible"  class="text-right">T.Gratuita</th>
                        <th v-if="columns.total_unaffected.visible" class="text-right">T.Inafecta</th>
                        <th v-if="columns.total_exonerated.visible" class="text-right">T.Exonerado</th>
                        <th v-if="columns.total_taxed.visible" class="text-right">T.Gravado</th>
                        <th v-if="columns.total_igv.visible" class="text-right">T.Igv</th>
                        <!-- <th v-if="columns.total_perception.visible" >Percepcion</th> -->
                        <th class="text-right">Total</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <!-- <td>{{ index }}</td> -->
                        <td class="text-left">{{ row.date_of_issue }}</td>
                        <td v-if="columns.date_of_due.visible" class="text-center">{{ row.date_of_due }}</td>
                        <td>{{ row.supplier_name }}<br/><small v-text="row.supplier_number"></small></td>
                        <td>{{row.state_type_description}}</td>
                        <td>{{ row.number }}<br/>
                            <small v-text="row.document_type_description"></small><br/>
                        </td>
                        <td>

                            <el-popover
                                placement="right"
                                width="400"
                                trigger="click">
                                <el-table :data="row.items">
                                    <el-table-column width="80" property="key" label="#"></el-table-column>
                                    <el-table-column width="220" property="description" label="Nombre"></el-table-column>
                                    <el-table-column width="90" property="quantity" label="Cantidad"></el-table-column>
                                </el-table>
                                <el-button slot="reference"> <i class="fa fa-eye"></i></el-button>
                            </el-popover>

                        </td>

                        <td class="text-center">{{ row.currency_type_id }}</td>
                        <td v-if="columns.total_free.visible" class="text-right">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ row.total_free }}</td>
                        <td v-if="columns.total_unaffected.visible" class="text-right">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ row.total_unaffected }}</td>
                        <td v-if="columns.total_exonerated.visible" class="text-right">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ row.total_exonerated }}</td>
                        <td v-if="columns.total_taxed.visible" class="text-right">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ row.total_taxed }}</td>
                        <td v-if="columns.total_igv.visible" class="text-right">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ row.total_igv }}</td>
                        <!-- <td v-if="columns.total_perception.visible" class="text-right">{{ row.total_perception ? row.total_perception : 0 }}</td> -->
                        <td class="text-right">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ row.total   }}</td>
                        <td class="text-right">

                            <a v-if="row.state_type_id != '11'" :href="`/${resource}/create/${row.id}`" type="button" class="btn waves-effect waves-light btn-xs btn-info">Editar</a>
                            <button v-if="row.state_type_id != '11'" type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickVoided(row.id)">Anular</button>
                            <button v-if="row.state_type_id == '11'" type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button>



                        </td>

                    </tr>
                </data-table>
            </div>
 
        </div>

         
    </div>
</template>

<script>

    import DataTable from '../../components/DataTable.vue'
    import {deletable} from '@mixins/deletable'


    export default {
        mixins: [deletable],
        components: {DataTable},
        data() {
            return {
                showDialogVoided: false,
                resource: 'fixed-asset/purchases',
                recordId: null,
                showDialogOptions: false,
                showDialogPurchasePayments: false,
                showImportDialog: false,
                columns: {
                    date_of_due: {
                        title: 'F. Vencimiento',
                        visible: false
                    },
                    total_free: {
                        title: 'T.Gratuita',
                        visible: false
                    },
                    total_unaffected: {
                        title: 'T.Inafecta',
                        visible: false
                    },
                    total_exonerated: {
                        title: 'T.Exonerado',
                        visible: false
                    },
                    total_taxed: {
                        title: 'T.Gravado',
                        visible: false
                    },
                    total_igv: {
                        title: 'T.Igv',
                        visible: false
                    },
                    // total_perception:{
                    //     title: 'Percepcion',
                    //     visible: false
                    // }

                }
            }
        },
        created() {
            this.loadColumnVisibility();
        },
        methods: { 
            saveColumnVisibility() {
                localStorage.setItem('columnVisibilityFixedpurchases', JSON.stringify(this.columns));
            },
            loadColumnVisibility() {
                const savedColumns = localStorage.getItem('columnVisibilityFixedpurchases');
                if (savedColumns) {
                    this.columns = JSON.parse(savedColumns);
                }
            },
            clickDownload(download) {
                window.open(download, '_blank');
            },
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },
            clickVoided(id)
            {
                this.anular(`/${this.resource}/voided/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
            clickDelete(id)
            {
                this.destroy(`/${this.resource}/delete/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>
