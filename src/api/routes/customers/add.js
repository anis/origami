const db = require('../../../database/client');

module.exports = async (req, res) => {
    const { name, headquarters, vat_identifier } = req.body;

    try {
        const result = await db.query(
            'INSERT INTO customers(name, headquarters, vat_identifier) VALUES($1, $2, $3) RETURNING customer_id',
            [name, headquarters, vat_identifier],
        );
        res.status(200).json({
            data: {
                customer_id: result.rows[0].customer_id,
                name,
                headquarters,
                vat_identifier,
            },
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
