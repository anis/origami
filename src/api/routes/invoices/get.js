const db = require('../../../database/client');
const { convertFromDb } = require('../../model/invoice');
const { generateInvoice } = require('../../helper/pdf');

module.exports = async (req, res) => {
    try {
        const { rows } = await db.query(
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
                costs.name AS costName,
                costs.quantity,
                costs.unity_price,
                costs.type
            FROM invoices
            INNER JOIN customers ON invoices.fk_customer_id = customers.customer_id
            INNER JOIN costs ON costs.fk_invoice_id = invoices.invoice_id
            WHERE invoices.invoice_id=$1
            ORDER BY costs.cost_id ASC`,
            [req.params.id],
        );

        const data = rows.reduce((previousValue, currentValue) => {
            const obj = previousValue !== undefined ? previousValue : currentValue;

            if (obj.costs === undefined) {
                obj.costs = [];
            }

            if (obj.total_amount === undefined) {
                obj.total_amount = 0;
            }

            obj.costs.push({
                name: currentValue.costname,
                quantity: currentValue.quantity,
                unity_price: currentValue.unity_price,
                type: currentValue.type,
            });

            obj.total_amount += currentValue.quantity * currentValue.unity_price;
            return obj;
        }, undefined);

        const finalData = convertFromDb(data);
        if (req.query.format === 'pdf') {
            try {
                const content = generateInvoice(finalData);

                res.status(200).setHeader('Content-type', 'application/pdf');
                res.send(content);
            } catch (e) {
                res.status(400).json({
                    message: e.message,
                });
            }
        } else {
            res.status(200).json({
                data: finalData,
            });
        }
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
