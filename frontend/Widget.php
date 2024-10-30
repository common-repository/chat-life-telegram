<script>
    document.addEventListener('DOMContentLoaded', function (event) {
        let chatBox = document.querySelector('.chatbox');
        let chatboxTitle = document.querySelector('.chatbox__title');
        let chatboxTitleClose = document.querySelector('.chatbox__title__close');

        chatboxTitle.onclick = (e) => {
            chatBox.classList.toggle('chatbox--tray')
        }
        chatboxTitleClose.onclick = (e) => {
            e.stopPropagation();
            chatBox.classList.toggle('chatbox--tray')
        }

            // let box = document.querySelector(".draggable");
            // let header = box.querySelector(".chatbox__title");
            //
            // addEvent(header,'mousedown', function (event) {
            //     console.log('mousedown')
            //     let e = event || window.event;
            //     let distX = pagePos(e).left - getStyle(box, "left");
            //     let distY = pagePos(e).top - getStyle(box, "top");
            //
            //     addEvent(document, "mousemove", move, false);
            //     addEvent(document, "mouseup", up, false);
            //
            //     function move(event) {
            //         let e = event || window.event,
            //             x = e.clientX - distX,
            //             y = e.clientY - distY;
            //         box.style.left = (x >= 0 ? x : 0) + 'px';
            //         box.style.top = (y >= 0 ? y : 0) + 'px';
            //     }
            //
            //     function up() {
            //         removeEvent(document, "mousemove", move, false);
            //         removeEvent(document, "mouseup", up, false);
            //     }
            // }, false);


        function addEvent(dom, type, fn, useCapture) {
            if (document.addEventListener) {
                dom.addEventListener(type, fn, useCapture);

            } if (document.attachEvent) {
                dom.attachEvent("on" + type, fn);

            } else {
                dom["on" + type] = fn;
            }
        }

        function removeEvent(dom, type, fn, useCapture) {
            if (document.removeEventListener) {
                dom.removeEventListener(type, fn, useCapture);

            } if (document.detachEvent) {
                dom.detachEvent("on" + type, fn);

            } else {
                dom["on" + type] = null;
            }
        }

        function pagePos(e) {
            var pLeft = getScrollOffset().left,
                pTop = getScrollOffset().top,
                cLeft = document.documentElement.clientLeft || 0,
                cTop = document.documentElement.clientTop || 0;

            return {
                left: e.clientX + pLeft - cLeft,
                top: e.clientY + pTop - cTop
            }
        }

        function getScrollOffset() {
            if (pageXOffset) {
                return {
                    left: pageXOffset,
                    top: pageYOffset
                }
            }

            return {
                left: document.body.scrollLeft + document.documentElement.scrollLeft,
                top: document.body.scrollTop + document.documentElement.scrollTop
            }
        }

        function getStyle(elem, prop) {
            if (getComputedStyle) {
                if (prop) {
                    return parseInt(getComputedStyle(elem, null)[prop]);
                }

                return getComputedStyle(elem, null);
            }

            if (prop) {
                return parseInt(elem.currentStyle[prop]);
            }

            return elem.currentStyle;
        }

    });
</script>


<div id="tcl-root-chat" class="tcl-theme-ios" >
    <div class="chatbox chatbox22 chatbox--tray draggable">
        <div class="chatbox__title">
            <h5>
                <?php echo esc_attr($title); ?>
            </h5>
            <span class="chatbox__title__close">
                <span>
                    <svg viewBox="0 0 16 16" width="16px" height="16px">
                      <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
                    </svg>
                </span>
            </span>
        </div>
        <div class="chatbox__body">
            <span v-if="messages.length < 1" class="welcome-notice">{{ welcomeText }}</span>
            <Message v-for="item in messages" :position="item.position" :data="item"></Message>
        </div>

        <div class="panel-footer">
            <div class="input-group message-wrap ">
                <input id="btn-input" type="text" class="form-control tcl-message-input input-sm chat_set_height"
                       placeholder="Type your message here..."
                       tabindex="0"
                       dir="ltr"
                       spellcheck="false"
                       autocomplete="off"
                       autocorrect="off"
                       autocapitalize="off"
                       v-model="inputText"
                       data-emojiable="true"
                       contenteditable="true" v-on:keyup.enter="send"/>
<!--                <span class="emoji-open">🙂</span>-->

                <span class="input-group-btn">
                 <button class="bt_bg btn-sm" id="btn-chat">
                     <svg v-if="loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                          width="38px" height="45px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                <g transform="rotate(0 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.9166666666666666s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(30 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.8333333333333334s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(60 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(90 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.6666666666666666s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(120 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.5833333333333334s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(150 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(180 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.4166666666666667s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(210 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.3333333333333333s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(240 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(270 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.16666666666666666s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(300 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                             begin="-0.08333333333333333s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g><g transform="rotate(330 50 50)">
                                  <rect x="47.5" y="24" rx="2.4" ry="2.4" width="5" height="12" fill="#1d3f72">
                                    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s"
                                             repeatCount="indefinite"/>
                                  </rect>
                                </g>
                             </svg>


                     <svg v-else xmlns="http://www.w3.org/2000/svg" v-on:click="send" width="25px" height="25px"
                          fill="#000" class="bi bi-send" viewBox="0 0 16 16" style="
                            transform: rotate(45deg); margin-top: 8px;">
                      <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                    </svg>


                </button>
                </span>
            </div>
        </div>
    </div>
</div>