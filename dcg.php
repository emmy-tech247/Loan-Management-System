<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Borrow Rates Per Month — DCG Cooperative</title>
<style>
  /* Reset */
  *, *::before, *::after { box-sizing: border-box; }

  /* Page */
  body {
    font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
    background: linear-gradient(180deg, #f4f7fb 0%, #eef4ff 100%);
    color: #0b2540;
    margin: 24px;
    display: flex;
    justify-content: center;
  }

  /* Card */
  .card {
    width: 100%;
    max-width: 900px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(12,34,64,0.08);
    padding: 28px;
    overflow: hidden;
  }

  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 8px;
  }

  header h1 {
    font-size: 20px;
    margin: 0;
    color: #032b56;
    letter-spacing: 0.2px;
  }

  header .sub {
    color: #475569;
    font-size: 13px;
    margin: 0;
  }

  .divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(3,43,86,0.06), transparent);
    margin: 18px 0;
  }

  /* Table wrapper */
  .table-wrap { overflow-x: auto; }

  table.rates {
    width: 100%;
    border-collapse: collapse;
    min-width: 640px;
    font-size: 14px;
  }

  table.rates thead th {
    text-align: left;
    padding: 12px 14px;
    background: linear-gradient(180deg,#f3f8ff,#eef7ff);
    color: #03305a;
    font-weight: 700;
    border-bottom: 1px solid #e6eefc;
    font-size: 13px;
  }

  table.rates tbody td {
    padding: 12px 14px;
    border-bottom: 1px dashed #eef4fb;
    vertical-align: middle;
    color: #122434;
  }

  table.rates th.col-sno,
  table.rates td.col-sno { width: 68px; text-align: center; }

  table.rates th.col-amount,
  table.rates td.col-amount { width: 60%; }

  table.rates th.col-rate,
  table.rates td.col-rate { text-align: right; width: 120px; font-weight: 600; font-variant-numeric: tabular-nums; }

  table.rates tbody tr:nth-child(even) { background: #fbfdff; }
  table.rates tbody tr:hover { background: #f1f8ff; }

  /* Policy note */
  .note {
    margin-top: 18px;
    background: #fbfbff;
    border-left: 4px solid #004080;
    padding: 12px 14px;
    color: #213447;
    border-radius: 6px;
    font-size: 13px;
  }

  /* Contact */
  .contact {
    margin-top: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .contact .name {
    font-weight: 700;
    color: #032b56;
  }

  .contact .phone {
    color: #064274;
    font-weight: 600;
  }

  /* Print friendly */
  @media print {
    body { margin: 0; background: #fff; }
    .card { box-shadow: none; border-radius: 0; }
  }

  /* Small screens */
  @media (max-width: 560px) {
    header { flex-direction: column; align-items: flex-start; gap: 6px; }
    table.rates thead th { font-size: 12px; }
    table.rates tbody td { font-size: 13px; padding: 10px; }
  }
</style>
</head>
<body>
  <div class="card" role="region" aria-label="Borrow rates per month">
    <header>
      <div>
        <h1>BORROW AT THE FOLLOWING RATES PER MONTH</h1>
        <p class="sub">Rates apply relative to member's saved amount</p>
      </div>
      <div style="text-align:right;">
        <small style="color:#6b7a8a">DCG Multipurpose Co-operative Society Ltd.</small>
      </div>
    </header>

    <div class="divider" aria-hidden="true"></div>

    <div class="table-wrap">
      <table class="rates" role="table" aria-label="Borrow rates table">
        <thead>
          <tr>
            <th class="col-sno" scope="col">S/NO</th>
            <th class="col-amount" scope="col">AMOUNT (N)</th>
            <th class="col-rate" scope="col">RATE (per month)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="col-sno" role="cell">1</td>
            <td class="col-amount" role="cell">Amount less than or equal to your saved amount</td>
            <td class="col-rate" role="cell">2%</td>
          </tr>
          <tr>
            <td class="col-sno">2</td>
            <td class="col-amount">Amount 2 × your saved amount</td>
            <td class="col-rate">3%</td>
          </tr>
          <tr>
            <td class="col-sno">3</td>
            <td class="col-amount">Amount 3 × your saved amount</td>
            <td class="col-rate">4%</td>
          </tr>
          <tr>
            <td class="col-sno">4</td>
            <td class="col-amount">Amount 4 × your saved amount</td>
            <td class="col-rate">5%</td>
          </tr>
          <tr>
            <td class="col-sno">5</td>
            <td class="col-amount">Amount 5 × and above your saved amount</td>
            <td class="col-rate">6%</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="note" role="note">
      <strong>Policy:</strong> Loans are intended to preserve capital; borrowers are required to service interest monthly while the principal (capital) remains outstanding. Please contact the office for eligibility and terms.
    </div>

    <div class="contact" aria-label="Cooperative contact">
      <div>
        <div class="name">Anyam T. Daniel, Ph.D.</div>
        <div style="font-size:13px; color:#566676">Cooperative President</div>
      </div>
      <div class="phone">08162577877</div>
    </div>
  </div>
</body>
</html>
