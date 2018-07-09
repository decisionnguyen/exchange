<template>
    <div>
        <ul class="nav nav-tabs tab-light market-tabs">
          <li role="presentation" class="active"><a @click="changeMarket('BTC', $event.target)"><img style="height: 16px;vertical-align: -3px;margin-right: 5px;" src="https://geniota.com/public/img/coins/btc.png"> BTC Market</a></li>
          <li role="presentation" @click="changeMarket('ETH', $event.target)"><a href="#"><img style="height: 16px;vertical-align: -3px;margin-right: 2px;" src="https://geniota.com/public/img/coins/eth.png"> ETH Market</a></li>
        </ul>
        <div class="currency-table wallet table-responsive">
            <div id="custom-search-input" class="search-custom" style="margin-bottom:0px;"><div class="input-group col-md-12" style="position: relative"><i style="position: absolute;top: 5px;left: 11px;color: grey;font-size: 22px; z-index: 3;" class="zmdi zmdi-search"></i><input v-model="searchText" style="padding-left: 34px;" type="text" placeholder="Search market..." class="form-control input-sm"></div></div>
            <table class="table market-table scroll-table" :style="'height:' + tableHeight + ';overflow:auto;margin-bottom:0px;'">
                <thead class="thead-grey">
                    <tr class="history-fonts">
                        <th v-if="show_col.indexOf('market') != -1 " @click="sortBy('name',$event)" scope="col">Market <i class="zmdi zmdi-unfold-more"></i></th>
                        <th v-if="show_col.indexOf('coinName') != -1" @click="sortBy('coin_name',$event)" scope="col">Coin name <i  class="zmdi zmdi-unfold-more"></i></th>
                        <th v-if="show_col.indexOf('lastPrice') != -1" @click="sortBy('lastPrice',$event)" scope="col">Last price <i class="zmdi zmdi-unfold-more"></i></th>
                        <th v-if="show_col.indexOf('change') != -1" @click="sortBy('change',$event)" scope="col">Change <i  class="zmdi zmdi-unfold-more"></i></th>
                        <th v-if="show_col.indexOf('24hvol') != -1" @click="sortBy('hoursVol',$event)" scope="col">24 hours vol <i  class="zmdi zmdi-unfold-more active"></i></th>
                        <th v-if="show_col.indexOf('24hhigh') != -1" @click="sortBy('hrHigh',$event)" scope="col">24hr high <i  class="zmdi zmdi-unfold-more"></i></th>
                        <th v-if="show_col.indexOf('24hlow') != -1" @click="sortBy('hrLow',$event)" scope="col">24hr low <i class="zmdi zmdi-unfold-more"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="market-row" v-for="market in filterByAndOrderBy()" v-bind:id="market.id" @click="goToTrade(market.id)">
                        <td v-if="show_col.indexOf('market') != -1" style="font-weight: 700">{{market.name}} <span v-if="market.is_ico" class="label label-info" style="vertical-align: middle;">ICO</span></td>
                        <td v-if="show_col.indexOf('coinName') != -1">{{market.coin_name}}</td>
                        <td :class="'number lastPrice-' + market.id" v-if="show_col.indexOf('lastPrice') != -1" v-html="formatFloatNumber(market.lastPrice)"></td>
                        <td class="number" v-if="show_col.indexOf('change')!= -1 && market.change == 0">0.00%</td>
                        <td v-if="show_col.indexOf('change')!= -1 && market.change > 0" :class="'green number change-' + market.id">{{parseFloat(market.change).toFixed(2)}}%</td>
                        <td v-if="show_col.indexOf('change')!= -1 && market.change < 0" :class="'red number change-' + market.id">{{parseFloat(market.change).toFixed(2)}}%</td>
                        <td class="number" v-if="show_col.indexOf('24hvol')!= -1">{{parseFloat(market.hoursVol).toFixed(8)}}</td>
                        <td class="number" v-if="show_col.indexOf('24hhigh')!= -1">{{parseFloat(market.hrHigh).toFixed(8)}}</td>
                        <td class="number" v-if="show_col.indexOf('24hlow')!= -1">{{parseFloat(market.hrLow).toFixed(8)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters';
    import functions from './../functions.js';

    export default {
        props: ['show_col', 'table_height', 'is_children'],
        data () {
            return {
                order : -1,
                sortKey : "hoursVol",
                searchText : "",
                marketName : "BTC",
                markets : [],
                tableHeight : 'auto',
                marketIndex : []
            }
        },
        sockets: {
            connect: function () {
                if(!this.is_children) {
                    $(".socket-status").find('img').attr('src', "https://geniota.com/public/img/socket-connected.png");
                    $(".socket-status").find('.content').html('Socket Connected');
                    this.$socket.emit('subscribe-to-channel', {channel: 'market'});
                    this.socket_status = 1;
                }
                
            },
            disconnect : function () {
                if(!this.is_children) {
                    $(".socket-status").find('img').attr('src', "https://geniota.com/public/public/img/socket-disconnected.png");
                    $(".socket-status").find('.content').html('Socket Disconnected');
                    this.socket_status = 0;
                }
            },
            /*
            "new-all-market-trade-history" : function (data) {
                var arr = data.message;
                var _this = this;
                var index = _this.marketIndex[arr.market_id];
                var market_symbol = arr.market_symbol;

                if(!_this.markets || _this.markets.length == 0) return;

                if(arr.price != _this.markets[market_symbol][index].lastPrice) {
                    _this.markets[market_symbol][index].lastPrice = arr.price;
                }

                _this.markets[market_symbol][index].hoursVol += parseFloat(arr.total);

                if(_this.markets[market_symbol][index].hrHigh < arr.price) _this.markets[market_symbol][index].hrHigh = parseFloat(arr.price);
                if(_this.markets[market_symbol][index].hrLow > arr.price) _this.markets[market_symbol][index].hrLow = parseFloat(arr.price);

                var priceOld = _this.markets[market_symbol][index].priceOld;
                var changePercent = ((arr.price - priceOld) / priceOld) * 100;
                var changePercentOld = _this.markets[market_symbol][index].change;

                if(changePercent != _this.markets[market_symbol][index].change) {
                    _this.markets[market_symbol][index].change = changePercent;

                    var element = $(".change-" + arr.market_id);

                    if(changePercent > 0) {
                        if(!element.hasClass('green')) {
                            element.removeClass('red');
                            element.addClass('green');
                        }
                    } else {
                        if(!element.hasClass('red')) {
                            element.removeClass('green');
                            element.addClass('red');
                        }
                    }

                    if(changePercent > changePercentOld) {
                        element.addClass('up');
                        setTimeout(function () {
                            element.removeClass('up');
                        },1000);
                    } else {
                        element.addClass('down');
                        setTimeout(function () {
                            element.removeClass('down');
                        },1000);
                    }

                }
            },
            */
        },
        mounted () {
            if(this.table_height) this.tableHeight = this.table_height;

            var _this = this;
            axios.get('/api/getAllMarkets').then(function (res){
                var data = res.data;

                if(data.error) return showAlert('error', data.error.message);

                _this.markets = data.data;

                for (var key in _this.markets) {
                    for(var key1 in _this.markets[key]) {
                        _this.marketIndex[_this.markets[key][key1].id] = parseInt(key1);
                    }
                }
            });
        },
        methods: {
            filterByAndOrderBy() {
                return this.orderBy(this.filterBy(this.markets[this.marketName], this.searchText),this.sortKey,this.order);
            },
            sortBy (col,event) {

                if(this.sortKey == col) {
                    if(this.order == 0) this.order = -1;
                    else this.order = 0;
                } else {
                    this.order = 0;
                    this.sortKey = col;
                }

                $(".currency-table .active").removeClass("active");
                $(event.target).addClass("active");
            },
            goToTrade(market_id) {
                window.location.href = '/trade/' + market_id;
            },
            changeMarket (market_name, target) {
                this.marketName = market_name;
                $('.market-tabs').find('.active').removeClass('active');
                $(target).parent().addClass('active');
            },
            formatFloatNumber (number) {
                return functions.formatFloatNumber(number);
            },
            cutFloatNumber (number) {
                return functions.cutFloatNumber(number);
            }
        }
    }
</script>
