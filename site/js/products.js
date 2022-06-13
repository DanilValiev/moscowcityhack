const url = "http://212.224.113.29:8999/api/product/get?perPage=27";
const searchUrl = "http://212.224.113.29:8999/api/product/search";
const filterUrl = "http://212.224.113.29:8999/api/product/filter"
let globalData = null;
const params = {};

if ((tmp_params = window.location.href.match(/.*\?.*/))) {
    for (let i = 0; i < tmp_params.length; i++) {
        const _tmp = window.location.href.replace(/.*\?/, '')
            .split('&')[i]
            .split('=');

        params[_tmp[0]] = _tmp[1];
    }
}

if (params['ogrn'] !== undefined && params['ogrn'] > 0) {
    filters(params['ogrn']);
} else {
    getData();
}

function getData(page = 1)
{
    $.get(`${url}&page=${page}`,
        function (data) {
            globalData = data;
            let companies = prepareHTML(data);

            let backUp = $("#products-list").html();

            $("#products-list").html(companies + backUp);
        }
    )
}

function search()
{
    let name = $("#search-field").val();
    if (name.length < 3) {
        return;
    }
    let companies = 'Прдукты не найдены';

    $.get(`${searchUrl}/${name}`,
        function (data) {
            globalData = data;
            companies = prepareHTML(data);

            $("#products-list").html(companies);
        });
}

function filters(ogrn = 0)
{
    let odkp = $("#odkp-filter").val();
    let capital = $("#ogrn-filter").val();

    if (ogrn !== 0) {
        capital = ogrn;
        odkp = 0;
    }

    let companies = 'Идет поиск';
    $("#products-list").html(companies);
    $.get(`${filterUrl}?odkp=${odkp}&ogrn=${capital}`,
        function (data) {
            globalData = data;
            companies = prepareHTML(data);

            $("#products-list").html(companies);
        });
}

function nexPage() {
    let page = globalData['meta']['page'] + 1;

    getData(page);
}

function prepareHTML(data) {
    let products = '';

    data['data'].forEach((element) => {

        let company = "Найденная компания";

        if (element['company'] !== undefined && element['company'] !== null) {
            company = element['company']['title'];
        }

        let photo = 'https://kubsafety.ru/image/catalog/revolution/404error.jpg';
        if (element['photo'] != null) {
            photo = element['photo'];
        }

        products += `
                    <div class="col-4">
                        <div class="card" style="width: 14rem;">
                            <div class="card-header">
                                <p class="topFont" style="line-height: 15px; margin-bottom: 15px;">${element['title']}</p>
                                <p class="littleFont" style="line-height: 15px;">${company}</p>
                            </div>
                            <img src="${photo}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <div class="card-description">
                                    <p>ОДКП2: ${element['odkp2']}</p>
                                    <p>ТНВД: ${element['tnved']}</p>
                                    <p>ГОСТ: ${element['gost']}</p>
                                    <p>Дата: ${element['createdAt']['date']}</p>
                                </div>
                                <a href="#" class="link-primary my-card-link">О ТОВАРЕ</a>
                            </div>
                        </div>
                    </div>
            `;

        if (element['title'] === undefined || element['title'] == null) {
        }
    });

    return products;
}