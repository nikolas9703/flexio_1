<template>

    <li class="dropdown hidden-xs hidden-sm">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle count-info" @click="setNotificationsAsRead()">
            <i class="fa fa-bell"></i>
            <span id="count_user" class="label label-warning" v-html="getCountNotificationsUnread"></span>
        </a>
        <ul id="lista_notificaciones" class="dropdown-menu dropdown-alerts content mCustomScrollbar minimal-dark">

            <li v-for="notification in notifications">
                <div class="dropdown-messages-box">
                    <a :href="notification.href" class="pull-left" style="width: 82px;">
                        <i class="fa" :class="notification.class" style="font-size: 44px;"></i>
                    </a>
                    <div class="media-body">
                        <small class="pull-right" v-html="notification.diff_horas"></small>
                        {{{notification.text}}} <br>
                        <small class="text-muted" v-html="notification.diff"></small>
                    </div>
                </div>

                <div class="divider"></div>
            </li>

        </ul>
    </li>


</template>

<script>

export default {

  props:{

        notifications: Array

    },

    methods: {

        getNotifications: function(setNotificationsAsRead = false){

            var context = this;
            context.$http.post({
                url: window.phost() + "usuarios/ajax-notifications",
                method:'POST',
                data:{erptkn: window.tkn, setNotificationsAsRead:setNotificationsAsRead}
            }).then(function(response){

                if(!_.isEmpty(response.data)){

                    var aux = JSON.parse(JSON.stringify(response.data));
                    var desktopNotifications = _.filter(aux, function(notification){
                        return notification.read_at == null && notification.data.to_desktop == true
                    });

                    context.notifications = aux;

                    if(!_.isEmpty(desktopNotifications))
                    {
                        context.notifyMe();
                    }
                }

            });

        },

        setNotificationsAsRead: function(){

            var context = this;

            if(context.getCountNotificationsUnread != 0)
            {
                context.getNotifications(true);
            }

        },

        showNotify: function(){

            var context = this;
            _.forEach(context.notifications, function(notification){
                if (notification.read_at == null && notification.data.to_desktop == true){
                    var tmp = document.createElement("DIV");
                    tmp.innerHTML = notification.text;
                    var aux = new Notification('Flexio', {
                            body: tmp.textContent || tmp.innerText || "",
                            tag: 'preset',
                            icon: phost() + 'public/themes/erp/images/logo_flexio_background_transparent_recortado_miniV1.png'
                    });
                }
            });

        },

        notifyMe: function () {

            var context = this;
            if (!("Notification" in window)) {
                alert("Este navegador no soporta notificaciones de escritorio");
            }

            else if (Notification.permission === "granted") {

                context.showNotify();

            }

            else if (Notification.permission !== 'denied') {
                Notification.requestPermission(function (permission) {
                    if (permission === "granted") {
                        context.showNotify();
                    }
                });
            }

        }

    },

    computed:{

        getCountNotificationsUnread: function(){

            var context = this;
            var aux = _.filter(context.notifications, function(notification){
                return notification.read_at == null;
            });
            return aux.length;

        }

    },

    data:function(){

        return {};

    },

    ready:function(){

        var context = this;
        context.getNotifications();

    }

}

</script>
