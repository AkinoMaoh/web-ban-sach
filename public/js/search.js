$(document).ready(function () {

    const searchInput = $('#searchInput');
    const searchDropdown = $('#searchDropdown');
    const searchContentBox = $('#searchContentBox');

    const searchUrl = '/api/search';

    searchInput.on('focus', function () {

        searchDropdown.fadeIn(150);

        loadDefaultSearch();

    });


   
    // Khi người dùng chưa nhập gì
    function loadDefaultSearch() {
        $.ajax({
            url: searchUrl,
            type: 'GET',
            data: {
                keyword: ''
            },
            success: function (response) {
                if (response.status !== 'suggestions') {
                    return;
                }
                let html = '';


                // Tìm kiếm gần đây
                const recentSearches = getRecentSearches();
                if (recentSearches.length > 0) {

                    html += `
                        <div class="search-section-title">
                            <i class="fas fa-history"></i>
                            TÌM KIẾM GẦN ĐÂY
                        </div>
                        <div class="recent-search-list">
                    `;


                    recentSearches.forEach(function (keyword) {
                        html += `

                            <div
                                class="recent-search-item"
                                data-keyword="${keyword}">
                                <i class="fas fa-history"></i>
                                <span>
                                    ${keyword}
                                </span>
                            </div>
                        `;
                    });

                    html += `
                        </div>
                    `;
                }


                // Từ khóa hot
                html += `
                    <div class="search-section-title">
                        <i class="fas fa-chart-line"></i>
                        TỪ KHÓA HOT
                    </div>
                    <div class="hot-keyword-grid">
                `;


                response.hot_keywords.forEach(function (keyword, index) {

                    html += `
                        <div
                            class="hot-keyword-item"
                            data-keyword="${keyword}">
                            <span class="hot-number">
                                #${index + 1}
                            </span>
                            <span>
                                ${keyword}
                            </span>
                        </div>
                    `;
                });

                html += `
                    </div>
                `;

                
                // Danh mục nổi bật
                html += `
                    <div class="search-section-title">
                        <i class="fas fa-th-large"></i>
                        DANH MỤC NỔI BẬT

                    </div>
                    <div class="category-grid">

                `;


                response.categories.forEach(function (category) {
                    html += `
                        <a href="/shop/category/${category.id}"
                        class="category-item">
                            <img src="${category.image}">
                            <div class="category-name">
                                ${category.name}
                            </div>
                        </a>
                    `;
                });
                html += `
                    </div>
                `;
                searchContentBox.html(html);
            },
            error: function () {
                searchContentBox.html(`
                    <div class="search-empty">
                        Có lỗi xảy ra khi tải dữ liệu.
                    </div>
                `);
            }
        });
    }


    // Tìm kiếm sản phẩm
    searchInput.on('keyup', function () {

        const keyword = $(this)
            .val()
            .trim();


        // Khi ko nhập keyword
        if (keyword === '') {
            loadDefaultSearch();
            return;
        }


        // Ajax tìm kiếm
        $.ajax({

            url: searchUrl,

            type: 'GET',

            data: {
                keyword: keyword
            },
            success: function (response) {
                let html = '';

                if (
                    response.status === 'products' &&
                    response.data.length > 0
                ) {
                    html += `
                        <div class="search-section-title">
                            <i class="fas fa-book"></i>
                            Sách tìm thấy
                        </div>
                    `;

                    // Hiển thị sản phẩm
                    response.data.forEach(function (product) {

                        let image = product.image
                            ? '/uploads/products/' + product.image
                            : '/images/no-image.png';


                        let authorName =
                            product.author
                                ? product.author.name
                                : 'Đang cập nhật';


                        
                        // Giá
                        let priceHtml = 'Liên hệ';

                        if (product.first_variant) {
                            const variant =
                                product.first_variant;

                            if (
                                variant.sale_price &&
                                variant.sale_price > 0 &&
                                variant.sale_price < variant.price
                            ) {

                                priceHtml = `
                                    <span class="search-product-price">
                                        ${Number(variant.sale_price)
                                            .toLocaleString('vi-VN')}
                                        VNĐ
                                    </span>

                                    <del class="search-product-old-price">
                                        ${Number(variant.price)
                                            .toLocaleString('vi-VN')}
                                        VNĐ
                                    </del>
                                `;

                            } else {
                                priceHtml = `
                                    <span class="search-product-price">
                                        ${Number(variant.price)
                                            .toLocaleString('vi-VN')}
                                        VNĐ
                                    </span>
                                `;
                            }

                        } else if (product.price) {
                            priceHtml = `
                                <span class="search-product-price">
                                    ${Number(product.price)
                                        .toLocaleString('vi-VN')}
                                    VNĐ
                                </span>
                            `;
                        }

                        html += `
                            <a
                                href="/product/${product.id}"
                                class="search-product-item">

                                <img
                                    src="${image}"
                                    class="search-product-img"
                                    alt="${product.name}">

                                <div class="search-product-info">
                                    <div class="search-product-name">

                                        ${product.name}

                                    </div>
                                    <div class="search-product-author">
                                        <i class="fas fa-pen-nib"></i>
                                        ${authorName}
                                    </div>
                                    <div>
                                        ${priceHtml}
                                    </div>
                                </div>
                            </a>
                        `;
                    });

                } else {
                    html = `
                        <div class="search-empty">
                            <i class="fas fa-search"></i>
                            <div>
                                Không tìm thấy sản phẩm
                            </div>
                        </div>
                    `;
                }
                searchContentBox.html(html);
                searchDropdown.fadeIn(150);
            },
            error: function () {
                searchContentBox.html(`
                    <div class="search-empty">
                        Có lỗi xảy ra khi tìm kiếm.
                    </div>
                `);
            }
        });
    });


    
    // Click từ khóa gần đây
    $(document).on(
        'click',
        '.recent-search-item',
        function () {
            const keyword =
                $(this).data('keyword');
            searchInput.val(keyword);
            searchInput.trigger('keyup');
        }
    );


    
    // Click từ khóa hot
    $(document).on(
        'click',
        '.hot-keyword-item',
        function () {
            const keyword =
                $(this).data('keyword');
            searchInput.val(keyword);
            searchInput.trigger('keyup');
        }
    );


    // Submit tìm kiếm (khi người dùng nhấn sumit thì tìm kiếm sẽ đc lưu)
    $('#searchForm').on('submit', function () {
        const keyword =
            searchInput.val().trim();
        if (keyword !== '') {
            saveRecentSearch(keyword);
        }
    });


    $(document).on('click', function (e) {
        if (
            !$(e.target)
                .closest('#headerSearchWrapper')
                .length
        ) {
            searchDropdown.fadeOut(100);
        }
    });


    
    // Lấy lịch sử tìm kiếm
    function getRecentSearches() {
        const data =
            localStorage.getItem('recentSearches');

        if (!data) {
            return [];

        }
        return JSON.parse(data);

    }


    
    // Lưu lịch sử tìm kiếm
    function saveRecentSearch(keyword) {

        let searches =
            getRecentSearches();


        
        // Xóa tìm kiếm cũ nếu đã tồn tại
        searches =
            searches.filter(function (item) {

                return item.toLowerCase()
                    !== keyword.toLowerCase();

            });
        
        // Đưa tìm kiếm mới lên đầu
        searches.unshift(keyword);


        searches =
            searches.slice(0, 4);
        localStorage.setItem(
            'recentSearches',
            JSON.stringify(searches)
        );
    }
});