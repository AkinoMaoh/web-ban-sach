$(document).ready(function () {

    $('#admin-search').keyup(function () {

        let keyword = $(this).val();

        if(keyword.trim().length < 1){
            $('#search-order-result').hide();
            return;
        }

        $.ajax({

            url: searchUrl,

            type: "GET",

            data: {
                keyword: keyword
            },

            success: function(data){

                let html = "";

                if(data.length > 0){

                    data.forEach(function(item){

                        let imageHtml = '';

                        if (item.image_url) {
                            imageHtml = `
                                <img
                                    src="${item.image_url}"
                                    class="admin-search-image"
                                    alt="${item[searchField]}"
                                >
                            `;
                        }

                        html += `
                            <a href="#" class="search-order-item">
                                ${imageHtml}

                                <span class="admin-search-name">
                                    ${item[searchField]}
                                </span>
                            </a>
                        `;

                    });

                    $('#search-order-result').html(html).fadeIn(150);

                }else{

                    $('#search-order-result').html(`
                        <div class="search-empty">
                            Không tìm thấy
                        </div>
                    `).show();

                }

            }

        });

    });

    $(document).click(function(e){

        if(!$(e.target).closest('form').length){

            $('#search-order-result').fadeOut(100);

        }

    });

    $(document).on("click", ".search-order-item", function(e){

        e.preventDefault();

        let keyword = $(this).text().trim();

        $("#admin-search").val(keyword);

        $("#search-order-result").fadeOut(100);

        $("#admin-search").closest("form").submit();

    });

});