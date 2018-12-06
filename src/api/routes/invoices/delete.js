const db = require('../../../database/client');

module.exports = async (req, res) => {
    try {
        await db.query(
            'UPDATE invoices SET canceled=TRUE WHERE invoice_id=$1',
            [req.params.id],
        );
        res.status(200).json({
            message: 'The invoice has been exterminated.',
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
