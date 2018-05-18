<template>
    <div class="currency-table-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="title-my-order">MY ORDER</h5>
                    <div class="table-left currency-table trading table-responsive">
                        <div style="height:450px;overflow:auto;width:100%">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">DATE</th>
                                        <th scope="col">TYPE</th>
                                        <th scope="col">PRICE</th>
                                        <th scope="col">AMOUNT</th>
                                        <th scope="col">VALUE</th>
                                        <th scope="col">TOTAL</th>
                                        <th scope="col" class="width-200"><button type="button" class="btn btn-danger custom-danger">CANCEL ALL</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in myOrders">
                                        <td>01/01/2018</td>
                                        <td class="red">SELL</td>
                                        <td>0.00000000</td>
                                        <td>0.00000000</td>
                                        <td>0.00000000</td>
                                        <td>0.00000000</td>
                                        <td><center><button type="button" class="btn btn-danger custom-danger-icon"><i class="zmdi zmdi-close"></i></button></center></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters'

    export default {
        props : ['market_id'],
        data () {
            return {
                myOrders : []
            }
        },
        mounted () {

            console.log(this.$parent);

            // this.myOrders = this.$parent.market.myOrders;
        },
        methods: {
            
        }
    }
</script>
