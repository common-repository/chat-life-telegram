const tclRoot = Vue.createApp({
    created() {
        this.update()
        this.welcomeText = Tcl.messageOne
        this.startUpdateInterval()
    },
    data() {
        return {
            messages: [],
            inputText: '',
            loader: false,
            idInterval: 0,
            welcomeText: '',
            isSend: false,
            waitingTime: 0,
            timeLastMessage: 0
        }
    },
    methods: {
        waitingTime() {

        },
        /**
         * start Update Interval
         */
        startUpdateInterval() {
            if (this.idInterval) return;
            this.idInterval = setInterval(this.update, 5000)
        },
        /**
         * scroll windows to  batton
         */
        scrollTop() {
            document.querySelector('.chatbox__body').scrollTop = document.querySelector('.chatbox__body').scrollHeight;

        },
        /**
         * send messages
         */
        send() {

            if (this.inputText.length < 1) {
                return;
            }

            if (this.isSend) return;

            this.loader = true

            this.fetch({
                text: this.inputText,
                method: 'send'
            }).then((data) => {

                if (data.status && data.messages) {
                //todo
                }

            });
            this.isSend = true;
            this.messages = [...this.messages, {
                avatar: '',
                date: '...',
                time: '...',
                message_id: '',
                send: false,
                position: 0,
                person: {
                    name: '....',
                    textMessage: this.inputText
                }

            }]

            setTimeout(() => {
                this.isSend = false;
            }, 2000)
            setTimeout(this.scrollTop, 200)
            this.inputText = '';
            this.update()
            return;

        },
        update() {
            let response = this.fetch({
                method: 'update'
            }).then((data) => {
                if (data.status && data.messages) {
                    const length = (this.messages).filter(ms => ms.send).length

                    if (length != data.messages.length) {
                        setTimeout(this.scrollTop, 200)
                        this.messages = data.messages ?? []
                    }

                }

            });


        },
        fetch: async function (data) {

            data.action = 'tcl_action'

            let dataForm = new FormData;

            for (const it in data) {
                dataForm.append(it, data[it])
            }
            const response = await fetch(Tcl.ajaxurl, {
                method: 'POST',
                body: dataForm

            })
            this.loader = false
            return await response.json();
        }

    }

})

tclRoot.component('Message', {
    template: `       
        <div class="chatbox__body__message" :message_id="data.message_id" 
         v-bind:class="{ 'chatbox__body__message--left' : position == 0 , 'chatbox__body__message--right' : position == 1}"        
                
         >         
          
            <div class="chatbox_timing">
                <ul>
                    <li><a href="#"><i class="fa fa-calendar"></i> {{ data.date }}</a></li>
                    <li><a href="#"><i class="fa fa-clock-o"></i> {{ data.time }} </a></li>
                </ul>
            </div>
          
            <span class="avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                </svg>
            </span>
            <div class="clearfix"></div>
            <div class="ul_section_full">
                <ul class="ul_msg">
                    <li><strong>{{ data.person.name }}</strong></li>
                    <li v-html="data.person.textMessage"></li>
                </ul>
                <div class="clearfix"></div>
<!--                <ul class="ul_msg2">-->
<!--                -->
<!--                    <li><a href="#"><i class="fa fa-pencil"></i></a></li>-->
<!--                    <li><a href="#"><i class="fa fa-trash chat-trash"></i></a></li>-->
<!--                    -->
<!--                </ul>-->
            </div>

        </div>
  `,
    props: {
        position: String,
        data: Object
    }
})

// const vm = app.mount('#tcl-root-chat')
const vm = tclRoot.mount('#tcl-root-chat')


/**
 * lib
 */

/**
 *
 * @param name
 * @return {string|undefined}
 */
function tclGetCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : null;
}

/**
 *
 * @param name
 * @param value
 * @param options
 */
function tclSetCookie(name, value, options = {}) {

    options = {
        path: '/',
        ...options
    };

    if (options.expires instanceof Date) {
        options.expires = options.expires.toUTCString();
    }

    let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
        updatedCookie += "; " + optionKey;
        let optionValue = options[optionKey];
        if (optionValue !== true) {
            updatedCookie += "=" + optionValue;
        }
    }

    document.cookie = updatedCookie;
}