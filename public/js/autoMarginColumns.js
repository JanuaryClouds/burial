export default function autoMarginColumns() {
    $('.row').each(function(index, element) {
        const totalCols = $(element).children();

        totalCols.each(function(index, element) {
            $(element).addClass('mb-6');

            if ($(element).is('[class^="col-"')) {
                return;
            }

            $(element).addClass('col-auto');
        });
    });
};