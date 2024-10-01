/**
 * GlobL variables.
 */
var temporaryMsgId = 0;

const messageForm = $(".message-form"),
    messageInput = $(".message-input"),
    messagechatBoxcontainer = $(".wsus__chat_area_body"),
    csrf_token = $("meta[name=csrf_token]").attr("content"),
    auth_id = $("meta[name=auth_id]").attr("content"),
    url = $("meta[name=url]").attr("content"),
    messengerContactBox = $(".messenger-contacts");
const getMessengerId = () => $("meta[name=id]").attr("content");
const setMessengerId = (id) => $("meta[name=id]").attr("content", id); //to get the user id inside content.

/**
 * Reuseable Functions.
 */
function enableChatboxloader() {
    $(".wsus__message_paceholder").removeClass("d-none");
}
function disableChatboxloader() {
    $(".wsus__chat_app").removeClass(".show_info");
    $(".wsus__message_paceholder").addClass("d-none");
    $(".wsus__message_paceholder_black").addClass("d-none");
}

function imagePreview(input, selector) {
    if (input.files && input.files[0]) {
        var render = new FileReader();
        render.onload = function (e) {
            $(selector).attr("src", e.target.result);
        };
        render.readAsDataURL(input.files[0]);
    }
}
let searchPage = 1; //first step to customize pagination via ajax.
let nomoreDatasearch = false; //this for if the content of input search not found and it will be not to send The request ajax.
let searchTempVal = "";
let setSearchloading = false;
function searchUsers(query) {
    searchTempVal = query;

    if (query != searchTempVal) {
        searchPage = 1;
        nomoreDatasearch = false;
    }
    if (!setSearchloading && !nomoreDatasearch) {
        $.ajax({
            method: "GET",
            url: "messenger-chat/search",
            data: { query: query, page: searchPage },
            beforeSend: function () {
                setSearchloading = false;
                let loader = `
                   <div class="text-center search-loader">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;
                $(".user_search_list_result").append(loader);
            },
            success: function (data) {
                setSearchloading = true;
                $(".user_search_list_result").find(".search-loader").remove(); //to remove the repeatate spinner.
                if (searchPage < 2) {
                    $(".user_search_list_result").html(data.records);
                } else {
                    $(".user_search_list_result").append(data.records);
                }
                nomoreDatasearch = searchPage >= data?.last_page;

                searchPage += 1;
            },
            error: function (xhr, status, error) {
                setSearchloading = false;
                $(".user_search_list_result").find(".search-loader").remove();
            },
        });
    }
}

function actiononScroll(selector, callback, topScroll = false) {
    $(selector).on("scroll", function () {
        let element = $(this).get(0); //to get the current Element.
        const condition = topScroll
            ? element.scrollTop == 0
            : element.scrollTop + element.clientHeight >= element.scrollHeight;

        if (condition) {
            callback();
        }
    });
}
function debounce(callback, delay) {
    let TimerId;
    return function (...args) {
        clearTimeout(TimerId);
        TimerId = setTimeout(() => {
            callback.apply(this, args);
        }, delay);
    };
}

/**
 * --------------------
 * Fetch id data of user and update the view.
 * --------------------
 */
function IDinfo(id) {
    $.ajax({
        method: "GET",
        url: "/messenger-id/info",
        beforeSend: function () {
            NProgress.start(); //to start work with nprogress npm.
            enableChatboxloader();
            MessageFormReset();
        },
        data: { id: id },
        success: function (data) {
            //to fetch the id of the Other user.
            fetchMessage(data.fetch.id, true);
            //to load the gallery
            $(".wsus__chat_info_gallery").html(""); //to display image users toggle.
            if (data?.shared_photos) {
                $(".nothing_share").removeClass("d-none");
                $(".wsus__chat_info_gallery").html(data.shared_photos);
            } else {
                $(".nothing_share").removeClass("d-none");
            }
            data.favorite > 0
                ? $(".favourite").addClass("active")
                : $(".favourite").removeClass("active");
            $(".messenger-header").find("img").attr("src", data.fetch.avatar);
            $(".messenger-header").find("h4").text(data.fetch.name);
            $(".user-info-view .user_photo")
                .find("img")
                .attr("src", data.fetch.avatar);
            $(".user-info-view .user_photo")
                .find(".user_name")
                .text(data.fetch.name);
            $(".user-info-view")
                .find(".user-unique-name")
                .text(data.fetch.name);
            NProgress.done(); //here if the data is loaded the progress will go away.
            disableChatboxloader();
        },
        error: function (xhr, status, error) {
            disableChatboxloader();
        },
    });
}

/**
 * --------------------
 * Send The Message.
 * --------------------
 */

function sendMessage() {
    temporaryMsgId += 1;
    let tempId = `temp_${temporaryMsgId}`;
    let hasAttachment = !!$(".message-input").val();
    const inputvalue = messageInput.val();
    if (inputvalue.length > 0 || hasAttachment) {
        const Formdata = new FormData($(".message-form")[0]);
        Formdata.append("id", getMessengerId()); //for the sender.
        Formdata.append("temporaryMsgId", tempId);
        Formdata.append("_token", csrf_token);
        $.ajax({
            method: "post",
            url: "/messengermsg/send-message",
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                //add temp message on dom.
                if (hasAttachment) {
                    messagechatBoxcontainer.append(
                        sendTempmessagecard(inputvalue, tempId, true)
                    );
                } else {
                    messagechatBoxcontainer.append(
                        sendTempmessagecard(inputvalue, tempId)
                    );
                }
                //for the reset operation to the form chat.
                MessageFormReset();
                scrollToBottom(messagechatBoxcontainer);
            },
            data: Formdata,

            success: function (data) {
                //update contact Item.
                updateContactItem(getMessengerId());
                const TempMsgCardElemet = messagechatBoxcontainer.find(
                    `.message-card[data-id="${data.tempId}"]`
                );
                TempMsgCardElemet.before(data.message);
                TempMsgCardElemet.remove(); //this to change the Tempid value.
            },
            error: function (xhr, status, error) {
                console.log(xhr);
            },
        });
    }
}
function sendTempmessagecard(message, tempId, attachment = false) {
    if (attachment) {
        return `
           <div class="wsus__single_chat_area message-card" data-id="${tempId}">
                    <div class="wsus__single_chat chat_right">
                        <div class="pre_loader">
                            <div class="spinner-border text-light" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <a class="venobox" data-gall="gallery01" href="images/chat_img.png">
                            <img src="{{ asset('chatasset') }}/images/chat_img.png" alt="gallery1" class="img-fluid w-100">
                        </a>
                        ${
                            message.length > 0
                                ? `<p class="messages">${message}</p>`
                                : ""
                        }

                   <span class="clock"><i class="fas fa-clock"></i>now</span>
                    </div>
                </div>

        `;
    } else {
        return `

                <div class="wsus__single_chat_area message-card" data-id="${tempId}">
                    <div class="wsus__single_chat chat_right">
                        <p class="messages">${message}</p>
                   <span class="clock"><i class="fas fa-clock"></i> 5h ago</span>
                    </div>
                </div>`;
    }
}

function receiveMessagecard(e) {
    if (e.attachment) {
        return `
           <div class="wsus__single_chat_area message-card" data-id="${e.id}">
                    <div class="wsus__single_chat">
                     <a class="venobox" data-gall="gallery${e.id}" href="${
            url + e.attachment
        }">
                <img src="${url + e.attachment}" alt="" class="img-fluid w-100">
            </a>
                        <a class="venobox" data-gall="gallery01" href="images/chat_img.png">
                            <img src="{{ asset('chatasset') }}/images/chat_img.png" alt="gallery1" class="img-fluid w-100">
                        </a>
                        ${
                            e.body.length > 0
                                ? `<p class="messages">${e.body}</p>`
                                : ""
                        }

                    </div>
                </div>

        `;
    } else {
        return `

                <div class="wsus__single_chat_area message-card" data-id="${e.id}">
                    <div class="wsus__single_chat">
                        <p class="messages">${e.body}</p>
                    </div>
                </div>`;
    }
}
function MessageFormReset() {
    $(".emojionearea-editor").text("");
    $(".attachment-block").addClass("d-none");
    messageForm.trigger("reset");
}
/**
 * --------------------
 * Fetch Message From Database.
 * --------------------
 */
let messagepage = 1;
let nomoremessages = false;
let messageloading = false; //for the request loading to waiting to the previous request.
function fetchMessage(id, newFetch = false) {
    if (newFetch) {
        messagepage = 1;
        nomoremessages = false;
    }
    if (!nomoremessages && !messageloading) {
        $.ajax({
            method: "GET",
            url: "/messenger/fetch-message",
            data: {
                _token: csrf_token,
                id: id,
                page: messagepage,
            },
            beforeSend: function () {
                messageloading = true;
                let loader = `
                <div class="text-center messages-loader">
             <div class="spinner-border text-primary" role="status">
                 <span class="visually-hidden">Loading...</span>
             </div>
         </div>`;
                messagechatBoxcontainer.prepend(loader);
            },
            success: function (data) {
                messageloading = false;
                //remove The Loader.
                messagechatBoxcontainer.find(".messages-loader").remove();
                //make messages seen.
                Makeseen(true);
                if (messagepage == 1) {
                    messagechatBoxcontainer.html(data.messages);
                    scrollToBottom(messagechatBoxcontainer);
                } else {
                    const lastMsg = $(messagechatBoxcontainer)
                        .find(".message-card")
                        .first();
                    const curoffset =
                        lastMsg.offset().top -
                        messagechatBoxcontainer.scrollTop();
                    messagechatBoxcontainer.prepend(data.messages);
                    messagechatBoxcontainer.scrollTop(
                        lastMsg.offset().top - curoffset
                    );
                }

                //pagination lock and page increment.
                nomoremessages = messagepage >= data?.last_page;
                if (!nomoremessages) messagepage += 1;
            },
            error: function (xhr, status, error) {},
        });
    }
}

/**
 * --------------------
 * Fetch contact List from Database.
 * --------------------
 */
let contactpage = 1;
let noMoreContact = false;
let contactLoading = false;
function getcontacts() {
    if (!noMoreContact && !contactLoading) {
        $.ajax({
            method: "GET",
            url: "/messenger/fetch-contact",
            data: { page: contactpage },
            beforeSend: function () {
                contactLoading = true;
                let loader = `
                 <div class="text-center contact-loader">
              <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
              </div>
          </div>`;
                messengerContactBox.append(loader);
            },
            success: function (data) {
                contactLoading = false;
                messengerContactBox.find(".contact-loader").remove();
                if (contactpage < 2) {
                    messengerContactBox.html(data.contacts);
                } else {
                    messengerContactBox.append(data.contacts);
                }
                noMoreContact = contactpage >= data?.last_page;
                if (!noMoreContact) contactpage += 1;
            },
            error: function (xhr, status, error) {
                contactLoading = false;
                messengerContactBox.find(".contact-loader").remove();
            },
        });
    }
}
/**
 * --------------------
 * Update Contact Item it's for push Element to the top.
 * --------------------
 */

function updateContactItem(user_id) {
    if (user_id != auth_id) {
        $.ajax({
            method: "GET",
            url: "/messenger/update-contact-item",
            data: { user_id: user_id },
            success: function (data) {
                messengerContactBox
                    .find(`.messenger-list-item[data-id="${user_id}"]`)
                    .remove();
                messengerContactBox.prepend(data.contact_item);
                if (user_id == getMessengerId()) updateSelectedContent(user_id);
            },

            error: function (xhr, status, error) {},
        });
    }
}
/**
 * --------------------
 * Make Messages Seen.
 * --------------------
 */
function Makeseen(status) {
    $(`.messenger-list-item[data-id="${getMessengerId()}"]`)
        .find(`.unseen_count`)
        .remove();
    $.ajax({
        method: "POST",
        url: "/messenger/make-seen",
        data: {
            _token: csrf_token,
            id: getMessengerId(),
        },
        success: function () {},
        error: function (xhr, status, error) {
            console.log(status);
        },
    });
}
/**
 * --------------------
 * Favorite.
 * --------------------
 */
function star(user_id) {
    $(".favourite").toggleClass("active");
    $.ajax({
        method: "POST",
        url: "/messenger/favorite",
        data: {
            _token: csrf_token,
            id: user_id,
        },
        success: function (data) {
            if (data.status == "added") {
                notyf.success("has been added to the favorite list.");
            } else {
                notyf.success("has been removed from the favorite list.");
            }
        },
        error: function (xhr, status, error) {
            console.log(status);
        },
    });
}
/**
 * --------------------
 * Delete a Message.
 * --------------------
 */

function DeleteMessage(message_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "DELETE",
                url: "/messenger/delete-message",
                data: { _token: csrf_token, message_id: message_id },
                beforeSend: function () {
                    $(`.message-card[data-id="${message_id}"]`).remove();
                },
                success: function (data) {
                    updateContactItem(getMessengerId());
                },
                error: function (xhr, status, error) {},
            });
        }
    });
}

function updateSelectedContent(user_id) {
    $(".messenger-list-item").removeClass("active");
    $(`.messenger-list-item[data-id="${user_id}"]`).addClass("active");
}
/**
 * --------------------
 * Slide to Bottom on Action.
 * --------------------
 */
function scrollToBottom(container) {
    $(container)
        .stop()
        .animate({
            scrollTop: $(container)[0].scrollHeight,
        });
}
/**
 * --------------------
 * Initialize venobox.js.
 * --------------------
 */

// function initVenobox(){
//     $('.venobox').venobox();
// }
/**
 * --------------------
 * message notification sound.
 * --------------------
 */

function playnotifysound(){
    const sound = new Audio(`/chatasset/8_message-sound.mp3`);
    sound.play();
}
//listen to the message channel.
window.Echo.private('message.' + auth_id).listen("Message", (e) => {
    console.log(e);
    if(getMessengerId() != e.from_id){
        updateContactItem(e.from_id);
        playnotifysound();
    }
    let message = receiveMessagecard(e);
    if(getMessengerId() == e.from_id){
    messagechatBoxcontainer.append(message);
    scrollToBottom(messagechatBoxcontainer);
    }
})

//listen to the online channel.

Window.Echo.join('online')
.here((users) => {
    console.log(users);  //here how many people already at the channel.
}).joining((user) => {

    console.log(user); //here the people will get notification you're joined to the channel.


}).leaving((user) => {
    console.log(user); //here the people will get notification you're leaved from the channel.

});

/**
 * --------------------
 * On Dom Load.
 * --------------------
 */

$(document).ready(function () {
    getcontacts();
    if (window.innerWidth < 768) {
        $("body").on("click", ".messenger-list-item", function () {
            $(".wsus__user_list").addClass("d-none");
        });
        $("body").on("click", ".back_to_list", function () {
            $(".wsus__user_list").removeClass("d-none");
        });
    }
    $("#select_file").change(function () {
        imagePreview(this, ".profile-image-preview");
    });

    //search action on keyup.
    const debounceSearch = debounce(function () {
        const value = $(".user_search").val();
        searchUsers(value);
    }, 500);
    $(".user_search").on("keyup", function () {
        let query = $(this).val();
        if (query.length > 0) {
            debounceSearch();
        }
    });

    //search pagination.
    actiononScroll(".user_search_list_result", function () {
        let value = $(".user_search").val();
        searchUsers(value);
    });

    //click action for messenger list item of users.
    $("body").on("click", ".messenger-list-item", function () {
        const dataId = $(this).attr("data-id");
        updateSelectedContent(dataId);
        setMessengerId(dataId);
        IDinfo(dataId);
        playnotifysound();
    });
    //Send message.
    $(".message-form").on("submit", function (e) {
        e.preventDefault();
        sendMessage();
    });

    //send attachment

    $(".attachment-input").change(function () {
        imagePreview(this, ".attachment-preview");
        $(".attachment-block").removeClass("d-none");
    });
    $(".canceled-attachment").on("click", function () {
        MessageFormReset();
    });
    //message pagination.
    actiononScroll(
        ".wsus__chat_area_body",
        function () {
            fetchMessage(getMessengerId());
        },
        true
    );
    //Contacts pagination.
    actiononScroll(
        ".messenger-contacts",
        function () {
            getcontacts();
        },
        true
    );
    //add or remove the favorite.

    $(".favourite").on("click", function (e) {
        e.preventDefault(); //it's for the dom start.
        star(getMessengerId());
    });

    //delete mesage.

    $("body").on("click", ".dlt-message", function (e) {
        e.preventDefault();
        let id = $(this).data(id);
        DeleteMessage(id);
    });
});
