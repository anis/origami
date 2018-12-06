const db = require('../../../database/client');
const { convertFromDb } = require('../../model/invoice');

module.exports = async (req, res) => {
    try {
        const result = await db.query(
            `SELECT
                invoices.invoice_id,
                invoiceIdentifier(invoices.issuing_date, invoices.invoice_id) AS invoice_identifier,
                invoices.deadline,
                invoices.issuing_date,
                invoices.service_beginning,
                invoices.service_end,
                invoices.canceled,
                customers.customer_id,
                customers.name,
                customers.headquarters,
                customers.vat_identifier,
                SUM(costs.quantity * costs.unity_price) AS total_amount
            FROM invoices
            INNER JOIN customers ON invoices.fk_customer_id = customers.customer_id
            INNER JOIN costs ON costs.fk_invoice_id = invoices.invoice_id
            GROUP BY invoices.invoice_id, customers.customer_id
            ORDER BY invoices.issuing_date DESC, invoices.canceled ASC, invoice_identifier DESC`,
        );
        res.status(200).json({
            data: result.rows.map(convertFromDb),
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
