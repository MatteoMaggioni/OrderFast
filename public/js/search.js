function search(id, input) {
	var filter, table, tr, td, i, txtValue;
	filter = input.value.toUpperCase();
	table = document.getElementById(id);
	tr = table.getElementsByTagName("tr");
	for (i = 1; i < tr.length; i++) {
		tds = tr[i].getElementsByTagName("td");
		find = false;
		for (j = 0; j < tds.length; j++) {
			if (tds[j]) {
				txtValue = tds[j].textContent || tds[j].innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					find = true;
					tr[i].style.display = "";
					break;
				}
			}
		}
		if (!find) {
			tr[i].style.display = "none";
		}
	}
}

const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

// do the work...
document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
	const table = th.closest('table');
	const tbody = table.closest('tbody');
	Array.from(tbody.querySelectorAll('tr'))
	  .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
	  .forEach(tr => tbody.appendChild(tr) );
})));