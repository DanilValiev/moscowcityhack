const url = "http://212.224.113.29:8999/api/company/get?perPage=27";
const searchUrl = "http://212.224.113.29:8999/api/company/search";
const filterUrl = "http://212.224.113.29:8999/api/company/filter"
let globalData = null;

getData();

function getData(page = 1)
{
    $.get(`${url}&page=${page}`,
        function (data) {
            globalData = data;
            let companies = prepareHTML(data);

            let backUp = $("#companies-list").html();

            $("#companies-list").html(companies + backUp);
        }
    )
}

function nexPage() {
    let page = globalData['meta']['page'] + 1;

    getData(page);
}

function search()
{
    let name = $("#search-field").val();
    if (name.length < 3) {
        return;
    }
    let companies = 'Компании не найдены';

    $.get(`${searchUrl}/${name}`,
        function (data) {
            globalData = data;
            companies = prepareHTML(data);

            $("#companies-list").html(companies);
        });
    $("#companies-list").html(companies);
}

function prepareHTML(data) {
    let companies = '';

    data['data'].forEach((element) => {
        companies += `
                <div class="col-4">
                        <div class="card" style="width: 14rem;">
                            <div class="card-header">
                                <p class="topFont">${element['title']}</p>
                            </div>
                            <div class="card-body">
                                <div class="card-description">
                                    <p style="margin-top: 10px">ИНН: ${element['inn']}</p>
                                    <p>ОГРН: ${element['ogrn']}</p>
                                    <p>ОДКП: ${element['odkpPrimary']['code']} - ${element['odkpPrimary']['title']}</p>
                                    <p>Адресс: ${element['address']}</p>
                                </div>
                                <a target="_blank" href="file:///Users/mklotz/PhpstormProjects/untitled1/companyDetail.html?id=${element['id']}" class="link-primary my-card-link">О ПРОИЗВОДИТЕЛЕ</a>
                            </div>
                        </div>
                </div>
            `;
    });

    return companies;
}

function filters()
{
    let odkp = $("#odkp-filter").val();
    let capital = $("#capital-filter").val();

    let companies = 'Компании не найдены';

    $.get(`${filterUrl}?odkp=${odkp}&capital=${capital}`,
        function (data) {
            globalData = data;
            companies = prepareHTML(data);

            $("#companies-list").html(companies);
        });
    $("#companies-list").html(companies);
}