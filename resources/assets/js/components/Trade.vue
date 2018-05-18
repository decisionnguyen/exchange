<template>
    <div class="currency-week" id="trading">
        <div class="container">

            <div class="ico-coin" v-if="market && market.is_ico && market.ico_infomation != null" :style="'background:' + market.ico_infomation.color">
                <div class="content">
                    <img class="logo" :src="$parent.SITE_URL + '/public/img/ico_coins/' + market.ico_infomation.logo">
                    <p class="name">{{ market.ico_infomation.name }}</p>
                    <p class="description">{{ market.ico_infomation.description }}</p>
                    <hr>
                    <div class="date">
                        <p class="start-date"><i class="fas fa-calendar"></i>Start date: {{ market.ico_infomation.start_date | moment("MMMM Do YYYY") }}</p>
                        <i class="fas fa-arrow-right"></i>
                        <p class="end-date"><i class="fas fa-calendar"></i>End date: {{ market.ico_infomation.end_date | moment("MMMM Do YYYY") }}</p>
                    </div>
                    <div class="social-link">
                        <a class="website" :href="market.ico_infomation.website"><i class="fas fa-globe"></i> <span>{{market.ico_infomation.website}}</span></a>
                        <a class="twitter" :href="'https://twitter.com/' + market.ico_infomation.twitter"><i class="fab fa-twitter"></i> <span>twitter.com/{{ market.ico_infomation.twitter }}</span></a>
                        <a class="facebook" :href="'https://facebook.com/' + market.ico_infomation.facebook"><i class="fab fa-facebook-f"></i> <span>facebook.com/{{ market.ico_infomation.facebook }}</span></a>
                    </div>
                </div>
                <div class="count-down">
                    <p class="title">ICO will be start: </p>
                    <div class="clock">
                        <div class="days number" style="display:none"><span class="clock-number">00</span><span class="clock-title">days</span></div>
                        <div class="hours number" style="display:none"><span class="clock-number">00</span><span class="clock-title">hours</span></div>
                        <div class="minutes number" style="display:none"><span class="clock-number">00</span><span class="clock-title">minutes</span></div>
                        <div class="seconds number" style="display:none"><span class="clock-number">00</span><span class="clock-title">seconds</span></div>
                    </div>
                </div>
                <img class="mark" :src="$parent.SITE_URL + '/public/img/ico_coins/' + market.ico_infomation.logo">
                <div style="clear:both"></div>
            </div>

            <div class="row">
                <div class="col-md-3">

                    <markets :table_height="'310px'" :show_col="['market', 'lastPrice', 'change']" :is_children="true"></markets>

                    <div class="table-left currency-table table-responsive">
                        <h2 style="color: black;">TRADE HISTORIES</h2>
                        <p class="sell-btc"></p>
                        <div style="height:330px;overflow:auto;width:100%">
                            <table class="table">
                                <thead class="thead-grey">
                                    <tr>
                                        <th scope="col">Price ({{ market.coinSecondSymbol }})</th>
                                        <th scope="col">Amount ({{ market.coinFirstSymbol }})</th>
                                        <th scope="col">Value ({{ market.coinSecondSymbol }})</th>
                                    </tr>
                                </thead>
                                <tbody class="market-trade-history">
                                    <tr v-if="market.tradeHistory" v-for="history in market.tradeHistory">
                                        <td style="padding-left: 8px;" v-if="history.type == 'buy' " class="green number">{{ roundNumber(history.price) }}</td>
                                        <td style="padding-left: 8px;" v-if="history.type == 'sell' " class="red number">{{ roundNumber(history.price) }}</td>
                                        <td style="padding-left: 8px;" class="number" v-html="formatFloatNumber(history.amount)"></td>
                                        <td style="padding-left: 8px;" class="number" v-html="formatFloatNumber(history.value)"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!--    Currency rate area start-->
                    <div class="currency-rate currency-rate-custom" style="margin: 0px;">
                        <h2 style="float: left;margin-left: 5px;margin-bottom:0px;">{{ market.name }} <span style="font-size: 80%;" class="number-grey">({{ market.coinName }})</span></h2>
                        <div class="currency-left" style="float:right;margin-right: 5px;">
                            <h2 class="number" style="font-size: 18px;margin-bottom:0px;" v-html="showLastPrice()"></h2>
                        </div>
                        <div style="clear: both"></div>
                        <hr style="margin: 10px 0px;">
                        <div class="col-md-4">
                            <div class="currency-left">
                                <h2 class="number" v-html="'24High: ' + formatFloatNumber(market.hrHigh)"></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="currency-left">
                                <h2 class="number" style="text-align:center;" v-html="'24Low: ' + formatFloatNumber(market.hrLow)"></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="currency-left">
                                <h2 class="number" style="text-align:right;" v-html="'24Vol: ' + formatFloatNumber(market.hoursVol)"></h2>
                            </div>
                        </div>
                    </div>
                    <!--    Currency rate area end-->
                    <!-- graph -->
                    <!-- TradingView Widget BEGIN -->
                    <div id="tv_chart_container"></div>
					<div class="tradingview-widget-container">
					</div>
					<!-- TradingView Widget END -->
                    <!-- /graph -->
                    <!-- buy-sell -->
                    <div class="row buy-sell-custom" style="margin:0px;">
                        <!-- SELECT TYPE -->
                        <div class="select-type-order">
                            <button type="button" class="active" @click="changeTypeOrder('limit', $event.target)">LIMIT</button>
                            <button type="button" @click="changeTypeOrder('market', $event.target)">MARKET</button>
                            <button type="button" @click="changeTypeOrder('stop-limit', $event.target)">STOP-LIMIT</button>
                        </div>
                        <!-- END SELECT TYPE -->
                        <div class="col-md-6">
                            <div class="buy currency-box">
                                <h3 v-if="is_login && market.balance" class="green">Balance: <span :class="'black number balance-' + market.coinSecondSymbol" v-html="formatFloatNumber(market.balance[market.coinSecondSymbol]) + ' ' + market.coinSecondSymbol"></span></h3>
                                <div style="clear: both;"></div>
                                <div class="single-box" v-if="orderType == 'stop-limit'">
                                    <h4>stop</h4>
                                    <h5>
                                        <input type="text" v-model="buyForm.stop" @blur="roundNumberWithAttr('buyForm', 'stop')">
                                        <span v-if="market.coinSecondSymbol">{{market.coinSecondSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box">
                                    <h4>Amount</h4>
                                    <h5>
                                        <input type="text" v-model="buyForm.amount" @blur="roundNumberWithAttr('buyForm', 'amount')">
                                        <span v-if="market.coinFirstSymbol">{{market.coinFirstSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box" v-if="orderType == 'limit' || orderType == 'stop-limit'">
                                    <h4>price</h4>
                                    <h5>
                                        <input type="text" v-model="buyForm.price" @blur="roundNumberWithAttr('buyForm', 'price')">
                                        <span v-if="market.coinSecondSymbol">{{market.coinSecondSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box" v-if="orderType == 'limit' || orderType == 'stop-limit'">
                                    <h4>value</h4>
                                    <h5>
                                        <input type="text" v-model="buyForm.total()">
                                        <span v-if="market.coinSecondSymbol">{{market.coinSecondSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box">
                                    <p v-if="!is_login" style="text-align:center"><a v-bind:href="$parent.SITE_URL + '/user/login' ">Login</a> or <a v-bind:href="$parent.SITE_URL + '/user/signup' ">Signup</a></p>
                                    <h5 v-if="is_login" class="red">
                                        <input @click="submitBuyOrder()" type="submit" value="buy">
                                    </h5>
                                    <div style="clear:both"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sell currency-box">
                                <h3 v-if="is_login && market.balance" class="red">Balance: <span :class="'black number balance-' + market.coinFirstSymbol" v-html="formatFloatNumber(market.balance[market.coinFirstSymbol]) + ' ' + market.coinFirstSymbol"></span></h3>
                                <div style="clear: both;"></div>
                                <div class="single-box" v-if="orderType == 'stop-limit'">
                                    <h4>stop</h4>
                                    <h5>
                                        <input type="text" v-model="sellForm.stop" @blur="roundNumberWithAttr('sellForm', 'stop')">
                                        <span v-if="market.coinSecondSymbol">{{market.coinSecondSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box" >
                                    <h4>Amount</h4>
                                    <h5>
                                        <input type="text" v-model="sellForm.amount" @blur="roundNumberWithAttr('sellForm', 'amount')">
                                        <span v-if="market.coinFirstSymbol">{{market.coinFirstSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box" v-if="orderType == 'limit' || orderType == 'stop-limit'">
                                    <h4>price</h4>
                                    <h5>
                                        <input type="text" v-model="sellForm.price" @blur="roundNumberWithAttr('sellForm', 'price')">
                                        <span v-if="market.coinSecondSymbol">{{market.coinSecondSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box" v-if="orderType == 'limit' || orderType == 'stop-limit'">
                                    <h4>value</h4>
                                    <h5>
                                        <input type="text" v-model="sellForm.total()">
                                        <span v-if="market.coinSecondSymbol">{{market.coinSecondSymbol}}</span>
                                    </h5>
                                </div>
                                <div class="single-box">
                                    <p v-if="!is_login" style="text-align:center"><a v-bind:href="$parent.SITE_URL + '/user/login' ">Login</a> or <a v-bind:href="$parent.SITE_URL + '/user/signup' ">Signup</a></p>
                                    <h5 v-if="is_login" class="red">
                                        <input @click="submitSellOrder()" type="submit" value="sell">
                                    </h5>
                                    <div style="clear:both"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /buy-sell -->
                </div>
                <div class="col-md-3">
            
                    <div class="currency-table-area margin-bttom">
                        <div class="table-left currency-table trading table-responsive">
                            <h2 class="red dislay-block-h2">Sell order</h2>
                            <p class="sell-btc number">{{ roundNumber(market.sum_sell_order) }} {{ market.coinFirstSymbol }}</p>
                            <div style="height:362px;overflow:auto;width:100%">
                                <table class="table sell-order-table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col">Price ({{ market.coinSecondSymbol }})</th>
                                            <th scope="col">Amount ({{ market.coinFirstSymbol }})</th>
                                            <th scope="col">Value ({{ market.coinSecondSymbol }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="sell in market.sellOrders">
                                            <td class="red number" @click="clickToPrice(roundNumber(sell.price))">{{ roundNumber(sell.price) }}</td>
                                            <td class="number" @click="clickToPrice(roundNumber(sell.amount))" v-html="formatFloatNumber(sell.amount)"></td>
                                            <td class="number" @click="clickToValue(roundNumber(sell.price),roundNumber(sell.amount))" v-html="formatFloatNumber(sell.value)"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="table-left currency-table trading table-responsive currency-table-buy">
                            <h2 class="dislay-block-h2">Buy order</h2>
                            <p class="sell-btc number">{{ roundNumber(market.sum_buy_order) }} {{ market.coinSecondSymbol }}</p>
                            <div style="height:362px;overflow:auto;width:100%">
                                <table class="table buy-order-table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col">Price ({{ market.coinSecondSymbol }})</th>
                                            <th scope="col">Amount ({{ market.coinFirstSymbol }})</th>
                                            <th scope="col">Value ({{ market.coinSecondSymbol }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="buy in market.buyOrders">
                                            <td class="green number" @click="clickToPrice(roundNumber(buy.price))">{{ roundNumber(buy.price) }}</td>
                                            <td class="number" @click="clickToAmount(roundNumber(buy.amount))" v-html="formatFloatNumber(buy.amount)"></td>
                                            <td class="number" @click="clickToValue(roundNumber(buy.price),roundNumber(buy.amount))" v-html="formatFloatNumber(buy.value)"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--Currency Table are end-->
                </div>
            </div>
            <div class="row" v-if="(market.myOrders && market.myOrders.length > 0) || (market.myStopLimitOrders && market.myStopLimitOrders.length > 0)">
                <div class="col-md-12">
                    <ul class="nav nav-tabs tab-light market-tabs" style="margin-top:20px;">
                        <li role="presentation" @click="myOrderType = 'limit'" :class="myOrderType == 'limit' ? 'active' : ''"><a>LIMIT ORDER</a></li>
                        <li role="presentation" @click="myOrderType = 'stop-limit'" :class="myOrderType == 'stop-limit' ? 'active' : ''"><a>STOP-LIMIT ORDER</a></li>
                    </ul>

                    <div v-if="myOrderType == 'limit' " class="table-left currency-table trading table-responsive my-order">
                        <div style="max-height:450px;overflow:auto;width:100%">
                            <table class="table">
                                <thead class="thead-grey">
                                    <tr>
                                        <th scope="col">DATE</th>
                                        <th scope="col">TYPE</th>
                                        <th scope="col">PRICE</th>
                                        <th scope="col">AMOUNT</th>
                                        <th scope="col">VALUE</th>
                                        <th scope="col">TOTAL</th>
                                        <th scope="col" class="width-200"><button type="button" class="btn btn-danger custom-danger" @click="cancelLimitAll()">CANCEL ALL</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in market.myOrders" :class="'my-order-' + order.id">
                                        <td>{{ order.created_at | moment("DD-MM-YYYY HH:mm:ss") }}</td>
                                        <td v-if="order.type == 'sell'" class="red">SELL</td>
                                        <td v-if="order.type == 'buy'" class="green">BUY</td>
                                        <td class="number">{{ roundNumber(order.price) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.amount) }} {{ market.coinFirstSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.value) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.total) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number"><center><button @click="cancelOrder(order.id, $event)" type="button" class="btn btn-danger custom-danger-icon"><i class="zmdi zmdi-close"></i></button></center></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="myOrderType == 'stop-limit' " class="table-left currency-table trading table-responsive my-order">
                        <div style="max-height:450px;overflow:auto;width:100%">
                            <table class="table">
                                <thead class="thead-grey">
                                    <tr>
                                        <th scope="col">DATE</th>
                                        <th scope="col">TYPE</th>
                                        <th scope="col">STOP</th>
                                        <th scope="col">PRICE</th>
                                        <th scope="col">AMOUNT</th>
                                        <th scope="col">VALUE</th>
                                        <th scope="col">TOTAL</th>
                                        <th scope="col" class="width-200"><button type="button" class="btn btn-danger custom-danger">CANCEL ALL</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in market.myStopLimitOrders" :class="'my-order-' + order.id">
                                        <td>{{ order.created_at | moment("DD-MM-YYYY HH:mm:ss") }}</td>
                                        <td v-if="order.type == 'sell'" class="red">STOP-SELL</td>
                                        <td v-if="order.type == 'buy'" class="green">STOP-BUY</td>
                                        <td class="number">{{ roundNumber(order.stop) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.price) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.amount) }} {{ market.coinFirstSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.value) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number">{{ roundNumber(order.total) }} {{ market.coinSecondSymbol }}</td>
                                        <td class="number"><center><button @click="cancelStopLimitOrder(order.id, $event)" type="button" class="btn btn-danger custom-danger-icon"><i class="zmdi zmdi-close"></i></button></center></td>
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
    import functions from './../functions.js';
    import Datafeeds from './../datafeed.js';
    import VueSocketio from 'vue-socket.io';

    Vue.use(VueSocketio, 'https://socket.geniota.com:8000');
    Vue.use(require('vue-moment'));

    export default {
        props : ['market_id', 'is_login', 'user_id', 'auth'],
        data () {
            return {
                market : {},
                orderType : 'limit',
                buyForm : {
                    amount : "0.00000000",
                    stop : "0.00000000",
                    price : "0.00000000",
                    total : function () {
                        return parseFloat(this.amount * this.price).toFixed(8);
                    }
                },
                sellForm : {
                    amount : "0.00000000",
                    stop : "0.00000000",
                    price : "0.00000000",
                    total : function () {
                        return parseFloat(this.amount * this.price).toFixed(8);
                    }
                },
                socket_status : 0,
                myOrderType: 'limit'
            }
        },
        mounted () {
            var _this = this;
            axios.get('/api/getMarketInfo/' + this.market_id).then(function (res){
                var data = res.data;
                if(data.success) {
                    _this.market = data.data;
                }

                if(!_this.market.myOrders || _this.market.myOrders.length == 0) {
                    _this.myOrderType = 'stop-limit';
                }

                if(_this.market.is_ico) {
                    _this.icoCountdown();
                }
            });

            // show charts
            _this.showCharts();
        }, 
        sockets: {
            connect: function () {
                $(".socket-status").find('img').attr('src', this.$parent.SITE_URL + '/public/img/socket-connected.png');
                $(".socket-status").find('.content').html('Socket Connected');
                this.$socket.emit('subscribe-to-channel', {channel: 'trade-' + this.market_id});

                if(this.is_login) {
                    this.$socket.emit('subscribe-to-channel', {channel: 'private-' + this.user_id, auth: this.auth});
                }
                
                this.socket_status = 1;
            },
            disconnect : function () {
                $(".socket-status").find('img').attr('src', this.$parent.SITE_URL + '/public/img/socket-disconnected.png');
                $(".socket-status").find('.content').html('Socket Disconnected');
                this.socket_status = 0;
            },
            "change-my-order" : function (data) {
                var arr = data.message;

                var _this = this;
                if(_this.market_id != arr.market_id) return;

                this.market.myOrders = arr.data;
            },
            "change-my-stop-limit-order" : function (data) {
                var arr = data.message;

                var _this = this;
                if(_this.market_id != arr.market_id) return;

                this.market.myStopLimitOrders = arr.data;
            },
            "change-balance" : function (data) {
                var arr = data.message;
                this.market.balance[Object.keys(arr)] = this.roundNumber(arr[Object.keys(arr)]);
                $(".balance-" + Object.keys(arr)).addClass('effect-number-change');
                setTimeout(function () {
                    $(".balance-" + Object.keys(arr)).removeClass('effect-number-change');
                },400);
            },
            "change-buy-order" : function (data) {
                var arr = data.message;
                this.market.buyOrders = arr;
            },
            "change-sell-order" : function (data) {
                var arr = data.message;
                this.market.sellOrders = arr;
            },
            "change-sum-buy-order" : function (data) {
                var arr = data.message;
                this.market.sum_buy_order = arr;
            },
            "change-sum-sell-order" : function (data) {
                var arr = data.message;
                this.market.sum_sell_order = arr;
            },
            "new-market-trade-history" : function (data) {
                var arr = data.message;

                var html = '<tr class="effect-new-history">';
                if(arr.type == 'buy')
                    html += '<td style="padding-left: 8px" class="number green">'+parseFloat(arr.price).toFixed(8);+'</td>';
                if(arr.type == 'sell')
                    html += '<td style="padding-left: 8px" class="number red">'+parseFloat(arr.price).toFixed(8);+'</td>';
                html += '<td style="padding-left: 8px" class="number">'+functions.formatFloatNumber(arr.amount)+'</td>';
                html += '<td style="padding-left: 8px" class="number">'+functions.formatFloatNumber(arr.value)+'</td>';
                html += '</tr>';

                $(".market-trade-history").prepend(html);
                $(".market-trade-history").find('tr:last-child').remove();

                this.market.oldPrice = this.market.lastPrice;
                this.market.lastPrice = arr.price;

                if(arr.price > this.market.hrHigh) this.market.hrHigh = arr.price;
                if(arr.price < this.market.hrLow) this.market.hrLow = arr.price;
                this.market.hoursVol = parseFloat(this.market.hoursVol) + parseFloat(arr.total);

            },
            "notification": function (data) {

                var arr = data.message;

                var _this = this;
                if(_this.market_id != arr.market_id) return;

                functions.showAlert('info', arr.data);
            }
        },
        methods: {
            showCharts () {
                var _this = this;

                if(!this.market.name) {
                    setTimeout(function () {
                        _this.showCharts();
                    },1000);
                    return;
                }

                var widget = window.tvWidget = new TradingView.widget({
                    // debug: true, // uncomment this line to see Library errors and warnings in the console
                    width: '100%',
                    height: 352,
                    symbol: _this.market.name,
                    interval: "D",
                    volume_precision: 8,
                    container_id: "tv_chart_container",
                    //  BEWARE: no trailing slash is expected in feed URL
                    datafeed: new Datafeeds.UDFCompatibleDatafeed,
                    library_path: _this.$parent.SITE_URL + "/public/charting_library/",
                    locale: "en",
                    //  Regression Trend-related functionality is not implemented yet, so it's hidden for a while
                    drawings_access: { type: 'black', tools: [ { name: "Regression Trend" } ] },
                    disabled_features: ["header_symbol_search"],
                    enabled_features: []
                });

                setTimeout(function () {
                    var b = $(document).find("iframe").contents().find(".chart-status-picture");
                    b.html("<span style='font-size: 12px;color:green;padding-right: 20px'><i style='width: 5px;height:5px;border-radius:50%;display:inline-block;vertical-align:middle;background:green'></i> realtime</span>")
                    // b.removeClass(b.attr("class")).addClass("chart-status-picture realtime-feed");
                },1000)
            },
            roundNumberWithAttr (attr1,attr2) {
                this[attr1][attr2] = parseFloat(this[attr1][attr2]).toFixed(8);
            },
            roundNumber (number) {
                return parseFloat(number).toFixed(8);
            },
            submitBuyOrder : function () {
                if(this.socket_status == 0) return functions.showAlert('error', 'Socket is disconnected. Please reload page')
                functions.waitMe('.buy');
                var _this = this;
                var data = {
                    market_id : this.market_id,
                    price : this.buyForm.price,
                    amount : this.buyForm.amount,
                    type: this.orderType,
                    stop: this.buyForm.stop
                }
                axios.post('/api/makeBuyOrder', data).then(function (res){
                    functions.hideWaitMe('.buy');
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    if(data.success) return functions.showAlert('success', data.success.message);
                });
            },
            submitSellOrder : function () {
                functions.waitMe('.sell');
                var _this = this;
                var data = {
                    market_id : this.market_id,
                    price : this.sellForm.price,
                    amount : this.sellForm.amount,
                    type: this.orderType,
                    stop: this.sellForm.stop
                }
                axios.post('/api/makeSellOrder', data).then(function (res){
                    functions.hideWaitMe('.sell');
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    if(data.success) return functions.showAlert('success', data.success.message);
                });
            },
            cancelOrder : function (order_id,$event) {
                functions.waitMe('.my-order');
                var _this = this;
                axios.get('/api/cancelOrder/' + order_id).then(function (res){
                    
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    functions.showAlert('success', data.success.message);
                    $(".my-order-" + order_id).remove();
                    location.reload();
                    functions.hideWaitMe('.my-order');
                });
            },
            solveCancelAll : function (data,index,callback) {

                if(data.length == index) return callback();

                var arr = data[index];

                var _this = this;

                axios.get('/api/cancelOrder/' + arr.id).then(function (res){
                    var res = res.data;
                    if(res.error) return functions.showAlert('error', res.error.message);
                    $(".my-order-" + arr.id).remove();
                    functions.showAlert('info', "Canceled "+(index+1)+" order. Remain "+(data.length - (index + 1))+" orders");
                    _this.solveCancelAll(data,index+1,callback);
                });

            },
            cancelLimitAll : function () {
                functions.waitMe('.my-order');
                var _this = this;

                this.solveCancelAll(_this.market.myOrders, 0, function () {
                    functions.showAlert('success', "Cancel all successful");
                    location.reload();
                    functions.hideWaitMe('.my-order');
                });
            },
            cancelStopLimitOrder : function (order_id,$event) {
                functions.waitMe('.my-order');
                var _this = this;
                axios.get('/api/cancelStopLimitOrder/' + order_id).then(function (res){
                    
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    functions.showAlert('success', data.success.message);

                    setTimeout(function () {
                        functions.hideWaitMe('.my-order');
                    },1000);
                });
            },
            clickToPrice : function (price) {
                this.sellForm.price = price;
                this.buyForm.price = price;
            },
            clickToAmount : function (amount) {
                this.sellForm.amount = amount;
                this.buyForm.amount = amount;
            },
            clickToValue : function (price,amount) {
                this.sellForm.price = price;
                this.buyForm.price = price;
                this.sellForm.amount = amount;
                this.buyForm.amount = amount;
            },
            formatFloatNumber (number) {
                return functions.formatFloatNumber(number);
            },
            cutFloatNumber (number) {
                return functions.cutFloatNumber(number);
            },
            showLastPrice() {
                if(!this.market.lastPrice) return this.roundNumber(0);
                if(this.market.lastPrice == this.market.oldPrice) return this.roundNumber(this.market.lastPrice);
                if(this.market.lastPrice > this.market.oldPrice) return '<span class="green"><i class="zmdi zmdi-long-arrow-up"></i> '+this.roundNumber(this.market.lastPrice)+'</span>';
                if(this.market.lastPrice < this.market.oldPrice) return '<span class="red"><i class="zmdi zmdi-long-arrow-down"></i> '+this.roundNumber(this.market.lastPrice)+'</span>';
            },
            icoCountdown () {
                var _this = this;
                var is_stated = Vue.moment() > Vue.moment(_this.market.ico_infomation.start_date);
                var dealine = _this.market.ico_infomation.start_date;
                if(is_stated) dealine = _this.market.ico_infomation.end_date;

                $(document).ready(function () {

                    if(is_stated) $(".count-down .title").html("ICO will be end: ");

                    $(".count-down").countdown(dealine, function(event) {

                        var days = event.strftime("%D");
                        if(days > 0) {
                            $(".days").show();
                            $(".days .clock-number").html(days);
                        }

                        var hours = event.strftime("%H");
                        if(hours > 0) {
                            $(".hours").show();
                            $(".hours .clock-number").html(hours);
                        }

                        var minutes = event.strftime("%M");
                        if(minutes > 0) {
                            $(".minutes").show();
                            $(".minutes .clock-number").html(minutes);
                        }

                        var seconds = event.strftime("%S");
                        if(seconds > 0) {
                            $(".seconds").show();
                            $(".seconds .clock-number").html(seconds);
                        }
                        
                    });
                })
            },
            changeTypeOrder (type, target) {
                var _this = this;
                _this.orderType = type;

                $(".select-type-order .active").removeClass('active');
                $(target).addClass('active');
            }
        }
    }
</script>
