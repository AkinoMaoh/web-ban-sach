$(document).ready(function () {

    $('#search-phone').keyup(function () {

        let phone = $(this).val();

        if(phone.trim().length < 1){
            $('#search-order-result').hide();
            return;
        }

        $.ajax({

            url: searchOrderUrl,

            type: "GET",

            data: {
                phone: phone
            },

            success: function(data){

                let html = "";

                if(data.length > 0){

                    data.forEach(function(order){

                        html += `
                            <a href="#" class="search-order-item">
                                ${order.shipping_phone}
                            </a>
                        `;

                    });

                    $('#search-order-result').html(html).fadeIn(150);

                }else{

                    $('#search-order-result').html(`
                        <div class="search-empty">
                            Không tìm thấy số điện thoại
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

    let phone = $(this).text().trim();

    $("#search-phone").val(phone);

    $("#search-order-result").fadeOut(100);

    $("#search-phone").closest("form").submit();

    });

});