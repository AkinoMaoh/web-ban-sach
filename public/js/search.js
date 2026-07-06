
$(document).ready(function(){
    $('#search').keyup(function(){
        let keyword = $(this).val();
        if(keyword.trim().length < 1) {
            $('#search-result').hide();
            return;
        }
        $.ajax({
            url: "/search-product",
            type: "GET",
            data: { keyword: keyword },
            success: function(data){
                let html = "";

                if(data.length > 0){
                    data.forEach(function(product){
                        html += `
                        <a href="/product/${product.id}" class="search-item">
                            <img src="/uploads/products/${product.image}" class="search-img">
                            <div class="search-content">
                                <div class="search-name">
                                    ${product.name}
                                </div>
                                <div class="search-price">
                                    ${Number(product.price).toLocaleString()} VNĐ
                                </div>
                            </div>
                        </a>
                        `;
                    });
                    $('#search-result').html(html).fadeIn(150);
                }else{
                    $('#search-result').html(`
                        <div class="search-empty">
                            Không tìm thấy sản phẩm
                        </div>
                    `).show();
                }
            }
        });
    });
    $(document).click(function(e){
        if(!$(e.target).closest('form').length) {
            $('#search-result').fadeOut(100);
        }
    });
});