(function () {
	tinymce.create("tinymce.plugins.zednaContactForm", {
		init: function (ed, url) {
			ed.addButton("zednaContactForm", {
				title: "Zedna Contact Form",
				image: url + "/zedna-contactform.gif",
				onclick: function () {
					let zednaEmail = prompt("Your e-mail", "admin@mysite.com");
					let zednaSubject = prompt("E-mail subject", "Message from website");

					if (zednaEmail && zednaSubject) {
						ed.execCommand(
							"mceInsertContent",
							false,
							'[contact email="' + zednaEmail + '" subject="' + zednaSubject + '"]');
					}
				}
			});
		},
		createControl: function (n, cm) {
			return null;
		},
		getInfo: function () {
			return {
				longname: "Zedna Contact Form",
				author: "Radek Mezuláník",
				authorurl: "http://www.mezulanik.cz",
				infourl: "",
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add(
		"zednaContactForm",
		tinymce.plugins.zednaContactForm
	);
})();
