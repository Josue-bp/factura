<template>
    <div v-loading="loading_submit">
        <div class="row ">
            <div class="col-md-12 col-lg-12 col-xl-12 filter-container">
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
                        <div class="d-flex">
                            <div class="d-flex align-items-center" style="width:100px">
                                Filtrar por:
                            </div>
                            <el-select
                                v-model="search.column"
                                placeholder="Select"
                                @change="changeClearInput"
                            >
                                <el-option
                                    v-for="(label, key) in columns"
                                    :key="key"
                                    :value="key"
                                    :label="label"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2">
                        <template
                            v-if="
                                search.column === 'date_of_issue' ||
                                    search.column === 'date_of_due' ||
                                    search.column === 'date_of_payment' ||
                                    search.column === 'date' ||
                                    search.column === 'delivery_date'
                            "
                        >
                            <el-date-picker
                                v-model="search.value"
                                type="date"
                                style="width: 100%;"
                                placeholder="Buscar"
                                value-format="yyyy-MM-dd"
                                @change="getRecords"
                            >
                            </el-date-picker>
                        </template>
                        <template v-else>
                            <el-input
                                placeholder="Buscar"
                                v-model="search.value"
                                style="width: 100%;"
                                prefix-icon="el-icon-search"
                                @input="getRecords"
                            >
                            </el-input>
                        </template>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2" v-if="records.length > 0">
                        <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>
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
                            <slot
                                v-for="(row, index) in records"
                                :row="row"
                                :index="customIndex(index)"
                            ></slot>
                        </tbody>
                    </table>
                    <div>
                        <el-pagination
                            @current-change="getRecords"
                            layout="total, prev, pager, next"
                            :total="pagination.total"
                            :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page"
                        >
                        </el-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import queryString from "query-string";

export default {
    props: {
        productType: {
            type: String,
            required: false,
            default: ''
        },
        resource: String,
        applyFilter: {
            type: Boolean,
            default: true,
            required: false
        },
    },
    data() {
        return {
            search: {
                column: null,
                value: null
            },
            columns: [],
            records: [],
            pagination: {},
            isVisible: false,
            loading_submit: false,
            showLeftShadow: false,
            showRightShadow: false,
        };
    },
    created() {
        this.$eventHub.$on("reloadData", () => {
            this.getRecords();
        });
        this.$root.$refs.DataTable = this;
    },
    async mounted() {
        await this.$http
            .get(`/${this.resource}/columns`)
            .then(response => {
                this.columns = response.data;
                this.search.column = _.head(Object.keys(this.columns));
            });
        await this.getRecords();

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
            
            this.showLeftShadow = scrollLeft > 1;
            this.showRightShadow = scrollRight > 1;
        },
        toggleInformation() {
            this.isVisible = !this.isVisible;
        },
        clickDownload(type) 
        {
            const query = queryString.stringify({
                ...this.search
            })

            window.open(`/${this.resource}/report/${type}/?${query}`, '_blank');
        },
        customIndex(index) {
            return (
                this.pagination.per_page * (this.pagination.current_page - 1) +
                index +
                1
            );
        },
        getRecords() {
            this.loading_submit = true;
            return this.$http
                .get(`/${this.resource}/records?${this.getQueryParameters()}`)
                .then(response => {
                    this.records = response.data.data;
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(
                        response.data.meta.per_page
                    );
                })
                .catch(error => {})
                .then(() => {
                    this.loading_submit = false;
                });
        },
        getQueryParameters() {
            if (this.productType == 'ZZ') {
                this.search.type = 'ZZ';
            }
            if (this.productType == 'PRODUCTS') {
                // Debe listar solo productos
                this.search.type = this.productType;
            }
            return queryString.stringify({
                page: this.pagination.current_page,
                limit: this.limit,
                ...this.search
            });
        },
        changeClearInput() {
            this.search.value = "";
            this.getRecords();
        },
        getSearch() {
            return this.search;
        }
    }
};
</script>
