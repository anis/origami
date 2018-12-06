const { toString } = require('../helper/date');

function formatIdentifier(issuingDate, rawIdentifier) {
    const year = issuingDate.getFullYear().toString().substr(-2);

    const rawMonth = issuingDate.getMonth() + 1;
    const month = `${rawMonth < 10 ? '0' : ''}${rawMonth}`;

    const identifier = rawIdentifier.toString().padStart(4, '0');

    return `F${year}${month}${identifier}`;
}

module.exports = {
    convertFromDb(dbInvoice) {
        return {
            invoice_id: dbInvoice.invoice_id,
            identifier: formatIdentifier(dbInvoice.issuing_date, dbInvoice.invoice_identifier),
            is_canceled: dbInvoice.canceled,
            deadline: toString(dbInvoice.deadline),
            issuing_date: toString(dbInvoice.issuing_date),
            service_beginning: toString(dbInvoice.service_beginning),
            service_end: toString(dbInvoice.service_end),
            customer: {
                customer_id: dbInvoice.customer_id,
                name: dbInvoice.name,
                headquarters: dbInvoice.headquarters,
                vat_identifier: dbInvoice.vat_identifier,
            },
            total_amount: dbInvoice.total_amount,
            costs: dbInvoice.costs,
        };
    },
};
