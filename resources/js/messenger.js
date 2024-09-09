/**
 * Reuseable Functions.
 */
function enableChatboxloader(){
    $(".wsus__message_paceholder").removeClass('d-none');
}
function disableChatboxloader(){
    $(".wsus__message_paceholder").addClass('d-none');
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
    if (query != searchTempVal) {
        searchPage = 1;
        nomoreDatasearch = false;
    }
    searchTempVal = query;

    if (!setSearchloading && !nomoreDatasearch) {
        $.ajax({
            method: "GET",
            url: "/messenger/search",
            data: { query: query, page: searchPage },
            beforeSend: function () {
                setSearchloading = true;
                let loader = `
                   <div class="text-center search-loader">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;
                $(".user_search_list_result").append(loader);
            },
            success: function (data) {
                setSearchloading = false;
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
        url: "/messenger/id-info",
        beforeSend: function(){
            NProgress.start();//to start work with nprogress npm.
            enableChatboxloader();
        },
        data: {id:id},
        success: function (data) {
            $(".messenger-header").find("img").attr("src",data.fetch.avatar);
            $(".messenger-header").find("h4").text(data.fetch.name);
            $(".user-info-view .user_photo").find("img").attr("src",data.fetch.avatar);
            $(".user-info-view .user_photo").find(".user_name").text(data.fetch.name);
            $(".user-info-view").find(".user-unique-name").text(data.fetch.name);
            NProgress.done();//here if the data is loaded the progress will go away.
            disableChatboxloader();
        },
        error: function (xhr, status, error) {},
    });
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
        IDinfo(dataId);
    });
});
