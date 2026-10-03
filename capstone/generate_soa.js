const PDFDocument = require("pdfkit");
const fs = require("fs");

const inputFile = process.argv[2];
const outputFile = process.argv[3];

if (!inputFile || !outputFile) {
    console.log("Missing input or output file.");
    process.exit(1);
}

if (!fs.existsSync(inputFile)) {
    console.log("Input JSON file does not exist.");
    process.exit(1);
}

const data = JSON.parse(
    fs.readFileSync(inputFile, "utf8")
);

const customerData = data.customer;
const payment = data.payment;


/* CREATE PDF */

const doc = new PDFDocument({
    margin: 50
});

const stream = fs.createWriteStream(outputFile);

doc.pipe(stream);


/* HEADER */

doc.fontSize(20)
    .font("Helvetica-Bold")
    .text(
        "MITZTIANPC WIRED INTERNET SERVICES",
        {
            align: "center"
        }
    );

doc.moveDown();

doc.fontSize(16)
    .text(
        "STATEMENT OF ACCOUNT",
        {
            align: "center"
        }
    );

doc.moveDown(1.5);


/* CUSTOMER INFORMATION */

doc.fontSize(13)
    .font("Helvetica-Bold")
    .text("CUSTOMER INFORMATION");

doc.moveDown(0.5);

doc.fontSize(11)
    .font("Helvetica");

doc.text(
    "Account Number: " +
    (customerData.account_number || "N/A")
);

doc.text(
    "Customer Name: " +
    customerData.f_name + " " +
    (customerData.m_name || "") + " " +
    customerData.l_name
);

doc.text(
    "Contact Number: " +
    (customerData.contact_number || "N/A")
);

doc.text(
    "Connection Status: " +
    (customerData.connection_status || "N/A")
);

doc.moveDown(1);


/* INTERNET SERVICE */

doc.fontSize(13)
    .font("Helvetica-Bold")
    .text("INTERNET SERVICE");

doc.moveDown(0.5);

doc.fontSize(11)
    .font("Helvetica");

doc.text(
    "Plan: " +
    (customerData.plan_name || "N/A")
);

doc.text(
    "Internet Speed: " +
    (customerData.internet_mbps || "N/A") +
    " Mbps"
);

doc.text(
    "Monthly Fee: PHP " +
    Number(customerData.internet_price || 0).toFixed(2)
);

doc.text(
    "Due Date: " +
    (customerData.due_date || "N/A")
);

doc.moveDown(1);

/* PAYMENT DETAILS */

doc.fontSize(13)
    .font("Helvetica-Bold")
    .text("PAYMENT DETAILS");

doc.moveDown(0.5);

doc.fontSize(11)
    .font("Helvetica");

doc.text(
    "Payment Date: " +
    payment.created_at
);

doc.text(
    "Payment Method: " +
    payment.payment_method
);

doc.text(
    "Amount Paid: PHP " +
    Number(payment.amount).toFixed(2)
);

doc.text(
    "Payment Status: " +
    payment.payment_status
);

if (payment.remarks) {
    doc.text(   
        "Remarks: " +
        payment.remarks
    );
}

doc.moveDown(1);
/* MONTHLY AMOUNT */

doc.moveDown();

doc.fontSize(14)
    .font("Helvetica-Bold")
    .text(
        "MONTHLY AMOUNT DUE: PHP " +
        Number(customerData.internet_price || 0).toFixed(2)
    );

doc.moveDown(1);

doc.fontSize(9)
    .font("Helvetica")
    .text(
        "This Statement of Account is generated electronically by MitztianPC Wired Internet Services."
    );


/* FINISH */

doc.end();

stream.on("finish", function () {
    console.log("PDF generated successfully.");
});