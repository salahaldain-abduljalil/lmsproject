/**
 * GlobL variables.
 */
var temporaryMsgId = 0;

const messageForm = $(".message-form"),
    messageInput = $(".message-input"),
    messagechatBoxcontainer = $(".wsus__chat_area_body"),
    csrf_token = $("meta[name=csrf_token]").attr("content");
const getMessengerId = () => $("meta[name=id]").attr("content");
const setMessengerId = (id) => $("meta[name=id]").attr("content", id); //to get the user id inside content.

/**
 * Reuseable Functions.
 */
function enableChatboxloader() {
    $(".wsus__message_paceholder").removeClass("d-none");
}
function disableChatboxloader() {
    $(".wsus__message_paceholder").addClass("d-none");
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
                nomoreDatasearch = searchPage >= data?.last_pages;

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
        },
        data: { id: id },
        success: function (data) {
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
            },
            data: Formdata,

            success: function (data) {
                const TempMsgCardElemet = messagechatBoxcontainer.find(
                    `.message-card[data-id=${data.tempId}]`
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
                                : ''
                        }

                   <span class="clock"><i class="fas fa-clock"></i>now</span>
                        <a class="action" href="#"><i class="fas fa-trash"></i></a>
                    </div>
                </div>

        `;
    } else {
        return `

                <div class="wsus__single_chat_area message-card" data-id="${tempId}">
                    <div class="wsus__single_chat chat_right">
                        <p class="messages">${message}</p>
                   <span class="clock"><i class="fas fa-clock"></i> 5h ago</span>
                  <a class="action" href="#"><i class="fas fa-trash"></i></a>
                    </div>
                </div>`;
    }
}
function MessageFormReset() {
    messageForm.trigger("reset");
    $(".emojionearea-editor").text("");
    $(".attachment-block").addClass("d-none");
}
/**
 * --------------------
 * On Dom Load.
 * --------------------
 */

$(document).ready(function () {
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
        setMessengerId(dataId);
        IDinfo(dataId);
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
});
