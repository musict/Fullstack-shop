document.querySelector('.sitemap-button').onclick = (e) => {

    e.preventDefault();
    createSitemap();
}

let links_counter = 0;

function createSitemap(){

    links_counter++;

    Ajax({data: {ajax: 'sitemap', create: 'true'}})
        .then((res) => {
            console.log('Успех - ' + res);
        })
        .catch ((res) => {
            console.log('Ошибка - ' + res);
        });
}
