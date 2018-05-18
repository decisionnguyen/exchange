<template>
    <div id="myModal" class="modal fade bs-example-modal-sm" role="dialog">
        <div class="modal-dialog modal-sm" style="min-width:333px;">

            <div class="modal-content">
                <form action="/user/login" method="POST" class="login-form">
                    <div class="modal-body">
                        <input type="hidden" name="_token" v-model="token">
                        <input type="hidden" name="username" v-model="username">
                        <input type="hidden" name="password" v-model="password">

                        <div class="g-recaptcha" data-sitekey="6LfF-kgUAAAAAN7MLzhuh4ExArx0p5pJ9y7UiBYi"></div>
                        </div>
                        <div class="modal-footer">
                            <button style="margin:0px;width:130px;" type="button" class="btn btn-sm" data-dismiss="modal">Close</button>
                            <button style="margin:0px;width:130px;" type="submit" class="btn btn-register btn-sm">Login</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        props : ['token','username','password'],
        mounted() {
            console.log('check robot mounted');
        }
    }
</script>
