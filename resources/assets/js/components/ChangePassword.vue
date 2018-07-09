<template>
    <div class="container">
        <div class="currency-box-information">
            <div class="setting-boder">
                <p class="title-setting">SETTING</p>
            </div>
            <div class="row">
                <accountsidebar></accountsidebar>
                <div class="col-md-9">
                    <center>
                        <form action="/user/changePassword" method="POST" style="margin-top:50px;">
                            <input type="hidden" name="_token" v-bind:value="csrf_token">
                            <input class="input-signup" type="password" name="old_password" placeholder="Old Password">
                            <input class="input-signup" type="password" name="new_password" placeholder="New password">
                            <input class="input-signup" type="password" name="retype_password" placeholder="Confirm new password">
                            <input class="btn-signup" type="submit" value="CHANGE PASSWORD">
                        </form>
                    </center>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters'

    export default {
        props : ['csrf_token'],
        data () {
            return {
                
            }
        },
        mounted () {
            
        },
        methods: {
            
        }
    }
</script>