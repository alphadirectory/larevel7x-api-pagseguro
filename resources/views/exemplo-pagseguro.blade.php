<h1> Olhe no console e verifique o código </h1>

<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

<script>
  init();
  setHash();

  function init() {
    fetch('/api/sessaoid')
      .then(res => res.json())
      .then(res => {
        window.PagSeguroDirectPayment.setSessionId(res.id);
        console.log(res)
      })
      .catch(err => {
        console.log(err)
      })
  }

  function setHash() {
    window.PagSeguroDirectPayment.onSenderHashReady(res => console.log(res));
  }

  // Informa qual a bandeira do cartão de acordo com o bin do cartão
  function setBandeira() {
    window.PagSeguroDirectPayment.getBrand({
      cardBin: 123456, // 6 primeiro digisto do cartao
      success: res => console.log(res),
      error: err => console.log(err),
    });
  }

  function setMetodoDePagament() {
    window.PagSeguroDirectPayment.getPaymentMethods({
      amount: 500.00,
      success: res => console.log(res),
      error: err => console.log(err),
    });
  }

  function tokenCartao() {
    window.PagSeguroDirectPayment.createCardToken({
      cardNumber: '0000 0000 0000 0000',
      brand: 'mastercard',
      cvv: '123',
      expirationMonth: 01,
      expirationYear: 2035,
      success: res => console.log(res),
      error: err => console.log(err)
    })
  }

  function pagar() {
    const payload = {
      sessionId: "c10d7b28e9744e37914f15811249d9bc",
      amount: "1.00",
      cardNumber: "0000 0000 0000 0000",
      cardBrand: {
        name: "mastercard",
        bin: 123456,
        cvvSize: 3,
        expirable: true,
        international: false,
        validationAlgorithm: "LUHN",
        config: {
          acceptedLengths: [
            16
          ],
          hasDueDate: true,
          hasCvv: true,
          hasPassword: false,
          securityFieldLength: 3
        }
      },
      tipo: "debito",
      cardToken: "d06d60b0b8fe42aaa513ede4cf708fe7",
      banco: "ITAU",
      hash: "f4bf9b67b92f921863f9fda987663285ecadb2bd5d9a85945d9f4bb9ec26891c"
    }

    fetch('/api/pagamento', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    }).then(res => res.json())
  }
</script>