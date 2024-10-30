/**
 * Telegram send chat life =)
 */

(function () {
    const loader = document.querySelector('.tcl-preloader')
    let webHook = {
        'add': document.querySelector('#tcl-wh-add'),
        'del': document.querySelector('#tcl-wh-del'),
        'input_result': document.querySelector('[name="tcl[webHook]"]')
    }
    if (webHook.add) {
        webHook.add.onclick = function (event) {
            postData(Tcl.ajaxurl, {action: 'tcl_admin_action', func: 'setWebHook', value: 1})
                .then((data) => {
                    webHook.input_result.value = data.description
                });
        }
        webHook.del.onclick = function (event) {
            postData(Tcl.ajaxurl, {action: 'tcl_admin_action', func: 'delWebHook', value: 1})
                .then((data) => {
                    webHook.input_result.value = data.description
                });
        }
    }

    async function postData(url = '', data = {}) {
        loader.classList.add('show')
        let formdata = new FormData;

        for (const item in data) {
            formdata.append(item, data[item])
        }


        // Default options are marked with *
        const response = await fetch(url, {
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *client
            body: formdata // body data type must match "Content-Type" header
        });
        loader.classList.remove('show')
        return await response.json(); // parses JSON response into native JavaScript objects
    }



    const modal = document.getElementById("listchat");
    const span = document.getElementsByClassName("close")[0];
    document.querySelectorAll(".open-chat").forEach(el => el.onclick = openChat);


    span.onclick = function () {
        modal.style.display = "none";

    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    function openChat(event){
        const sessionid = event.target.dataset.id
        let doc = document.querySelector('.tcl-chat-rezult')
        doc.innerHTML = ''
        postData(Tcl.ajaxurl, {action: 'tcl_admin_action', func: 'listChat', id : sessionid})
            .then((data) => {
                let  template = '';
                if (data){
                    data.forEach(data=>{

                        template +=  `       
                            <div class="chatbox__body__message ${ (data.position == 0) ?'chatbox__body__message--left' : 'chatbox__body__message--right'}"              >         
                              
                                <div class="chatbox_timing">
                                    <ul>                                       
                                        <li><a href="#"><i class="fa fa-clock-o"></i> ${ new Date(parseInt(data.create_at )*1000)  } </a></li>
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
                                        <li><strong>${data.name}</strong></li>
                                        <li v-html="data.person.textMessage">${data.data}</li>
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
                      `;

                    })
                    doc.insertAdjacentHTML('afterbegin',template);
                }

            });


        modal.style.display = "block";


    }


    return webHook;

})();
