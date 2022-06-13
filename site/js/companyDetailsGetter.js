const params = {};
const url = "http://212.224.113.29:8999/api/company/detail/";

if ((tmp_params = window.location.href.match(/.*\?.*/))) {
    for (let i = 0; i < tmp_params.length; i++) {
        const _tmp = window.location.href.replace(/.*\?/, '')
            .split('&')[i]
            .split('=');

        params[_tmp[0]] = _tmp[1];
    }
}

if (params['id'] !== undefined && params['id'] > 0) {
    $.get(`${url}${params['id']}`,
        function (data) {
            //Основное инфо
            $("#general-title").html(data['title']);
            $("#detail-title").html(data['title']);
            $("#detail-address").html(data['address']);
            $("#detail-inn").html(data['7702045083']);
            $("#detail-ogrn").html(data['1027700133988']);
            $("#detail-reg").html(data['regDate']);
            $("#detail-capital").html(data['statutoryCapital'] + " Рублей");
            $("#org-products").html(`<a id="detail-capital" target="_blank" href="file:///Users/mklotz/PhpstormProjects/untitled1/products.html?ogrn=${data['ogrn']}">Товары</a>`)

            //Основной одкп
            $("#primary-okpd-code").html(data['odkpPrimary']['code']);
            $("#primary-okpd-title").html(data['odkpPrimary']['title']);

            //Дополнительный одкп
            let odkpSec = '';
            if (data['odkpSecondary'] != null) {
                data['odkpSecondary'].forEach( (element) => {
                    odkpSec += `<div class="item"> <b>${element['code']}:</b> <p>${element['title']}</p> </div>`
                });
                $("#okpd-sec-info").html(odkpSec);
            }

            //Контакты
            $("#contact-fio").html(data['contacts']['contactFio'].length === 0 ? 'Нет данных' : data['contacts']['contactFio']);
            $("#contact-director").html(data['contacts']['directorFio'] == null || data['contacts']['directorFio'].length === 0 ? 'Нет данных' : data['contacts']['directorFio']);
            $("#contact-email").html(data['contacts']['email'].length === 0 ? 'Нет данных' : data['contacts']['email']);
            $("#contact-phone").html(data['contacts']['phone'].length === 0 ? 'Нет данных' : data['contacts']['phone']);
            $("#contact-fax").html(data['contacts']['fax']);

            //Фин отчетность
            let finReport = '';
            for (let key in data['finReport']) {
                let info = data['finReport'][key]['income'] - data['finReport'][key]['outcome'];
                finReport += `<div class="item"> <b>${key}:</b> <p>${info} Рублей</p> </div>`;
            }
            $("#fin-report").html(finReport);

            //Гос помощь
            let support = '';
            data['support'].forEach( (element) => {
                support += `<div class="item"> <b>${element['accept_date']}:</b> <p>${element['s']} Рублей</p> </div>`
            });
            $("#fin-support").html(support);
    })
}