const inputs = document.querySelectorAll("input[name='otp_digit[]']"),
  button = document.querySelector("button");
// iterate over all inputs
inputs.forEach((input, index1) => {
  input.addEventListener("keyup", (e) => {

    console.log("Key pressed in input", index1 + 1);

    const currentInput = input,
      nextInput = input.nextElementSibling,
      prevInput = input.previousElementSibling;
    if (currentInput.value.length > 1) {
      currentInput.value = "";
      return; 
    }
    if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
      nextInput.removeAttribute("disabled");
      nextInput.focus();
    }
    if (e.key === "Backspace") {
      inputs.forEach((input, index2) => {
        if (index1 <= index2 && prevInput) {
          input.setAttribute("disabled", true);
          input.value = "";
          prevInput.focus();
        }
      });
    }
    if (!inputs[5].disabled && inputs[5].value !== "") {
      button.classList.add("active");
      return;
    }
    button.classList.remove("active");
  });
});

document.getElementById('otpForm').addEventListener('submit', function (e) {
  const otp = Array.from(inputs).map(input => input.value).join('');
  document.getElementById('otp').value = otp;
  console.log("Generated OTP:", otp); // Log the generated OTP
});

window.addEventListener("load", () => inputs[0].focus());
