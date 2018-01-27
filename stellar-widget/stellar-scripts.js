function multiplyBy()
{
	num1 = document.getElementById("curprice").value;
	console.log(num1);
	num2 = document.getElementById("stellaramount").value;
	console.log(num2);
	document.getElementById("amountinusd").innerHTML = (num1 * num2).toLocaleString('en-US', {style: 'currency', currency: 'USD'});
}



