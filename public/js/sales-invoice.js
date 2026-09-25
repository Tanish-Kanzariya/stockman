    document.getElementById('printInvoiceBtn').addEventListener('click', ()=>{
        window.print();
    })

    //PDF code
    const invoiceNumber = invoice.dataset.invoiceNumber;
    document.getElementById('downloadPdfBtn').addEventListener('click',()=>{
        const invoice = document.getElementById('invoice');

        const options = {
            margin :10,

            filename: invoiceNumber + '.pdf',

            image:{
                type: 'jpeg',
                quality: 0.98
            },

            html2canvas:{
                scale:2
            },
            
            jsPDF: {
                unit: 'mm',

                format: 'a4',

                orientation: 'portrait'
            }
        };

        html2pdf()
            .set(options)
            .from(invoice)
            .save();
    });

    //Watsapp sharing code

    document.getElementById('shareWatsappBtn').addEventListener('click', async function(){
        const invoice = document.getElementById('invoice');

        const invoiceNumber = invoice.dataset.invoiceNumber;

        const options = {
            margin : 5,

            image:{
                type: 'jpeg',
                quality: 0.98
            },

            html2canvas:{
                scale:2,
                useCORS: true
            },

            jsPDF:{
                unit: 'mm',
                format: 'a4',
                orientation:'portrait'
            }
        };

        try{
            
            //Generate PDF as Blob
            const pdfBlob = await html2pdf()
                .set(options)
                .from(invoice)
                .outputPdf('blob');

            //Create actual PDF file
            const pdfFile = new File(
                [pdfBlob],
                invoiceNumber + '.pdf',

                {
                    type: 'application/pdf'
                }
            );

            //Checking if file is supported
            if(
                navigator.share &&
                navigator.canShare &&
                navigator.canShare({files: [pdfFile]})
            ){
                await navigator.share({
                    title: 'Sale Invoice',

                    text: 'Invoice ' + invoiceNumber,

                    files: [pdfFile]
                });
            }else{
                alert('File sharing is not supported on this browser. Please download the PDF and share it manually');

            }
        }catch(error){
            //User may cancel the share dialog
            if(error.name !== 'AbortError'){
                console.error('Share error:', error);
                alert('Unable to share invoice');
            }
        }
    });