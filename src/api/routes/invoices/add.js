const db = require('../../../database/client');
const { toString, fromTimestamp } = require('../../helper/date');
const { generateInvoice } = require('../../helper/pdf');

module.exports = async (req, res) => {
    const {
        customer_id, deadline, issuing_date, service_beginning, service_end, costs,
    } = req.body;

    // dates
    const issuingDate = issuing_date ? new Date(issuing_date * 1000) : new Date();
    const formattedDates = {
        issuing: toString(issuingDate, 'us'),
        beginning: fromTimestamp(service_beginning, 'us'),
        end: fromTimestamp(service_end, 'us'),
    };

    formattedDates.deadline = new Date(issuingDate.getTime());
    formattedDates.deadline.setDate(formattedDates.deadline.getDate() + deadline);

    if (req.query.preview !== undefined) {
        const { rows } = await db.query('SELECT * FROM customers WHERE customers.customer_id = $1', [customer_id]);

        try {
            const content = generateInvoice({
                customer: {
                    name: rows[0].name,
                    headquarters: rows[0].headquarters,
                    vat_identifier: rows[0].vat_identifier,
                },
                costs: costs.map(cost => ({
                    name: cost.label,
                    quantity: cost.quantity,
                    unity_price: cost.price,
                    type: cost.type,
                })),
                identifier: 'X',
                issuing_date: toString(issuingDate, 'fr'),
                deadline: toString(formattedDates.deadline, 'fr'),
                service_end: fromTimestamp(service_end, 'fr'),
            });

            res.status(200).setHeader('Content-type', 'application/pdf');
            res.send(content);
        } catch (e) {
            res.status(400).json({
                message: e.message,
            });
        }
    } else {
        try {
            await db.query('BEGIN');

            const { rows } = await db.query(
                'INSERT INTO invoices(deadline, issuing_date, service_beginning, service_end, status, canceled, fk_customer_id) VALUES($1, $2, $3, $4, $5, FALSE, $6) RETURNING invoice_id',
                [
                    formattedDates.deadline,
                    formattedDates.issuing,
                    formattedDates.beginning,
                    formattedDates.end,
                    true,
                    customer_id,
                ],
            );

            const request = costs.map((cost, i) => `($${(i * 5) + 1}, $${(i * 5) + 2}, $${(i * 5) + 3}, $${(i * 5) + 4}, $${(i * 5) + 5})`);
            const values = costs.map(cost => [
                cost.label,
                cost.quantity,
                cost.price,
                cost.type || null,
                rows[0].invoice_id,
            ]);

            await db.query(
                `INSERT INTO costs(name, quantity, unity_price, type, fk_invoice_id) VALUES ${request.join(', ')}`,
                values.flat(),
            );

            await db.query('COMMIT');

            res.status(200).json({
                data: {
                    invoice_id: rows[0].invoice_id,
                    deadline: formattedDates.deadline,
                    issuing_date: formattedDates.issuing,
                    service_beginning: formattedDates.beginning,
                    service_end: formattedDates.end,
                },
            });
        } catch (error) {
            await db.query('ROLLBACK');
            res.status(400).json({
                error: error.message,
            });
        }
    }
};
