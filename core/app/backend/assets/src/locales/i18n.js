import { createI18n } from "vue-i18n";
/**
 * Adding Translation for internationalization
 */
const i18n = createI18n({
	legacy: false,
	locale: adminLocale,
	fallbackLocale: "en",
	globalInjection: true,
	messages: {
		en: {
			pluginName: "Avif express",
			tagline:
				"Converts existing and newly uploaded Images to lite-weight AVIF format.",
			hireMe: "hire me",
			autoConvertLabel:
				"Automatically convert images to AVIF format on upload.",
			rendering: "Rendering",
			iamgeQuality: "Image Quality",
			compressionSpeed: "Compression Speed",
			uploadDirectory: "Upload directory",
			themeDirectory: "Theme Directory",
			convert: "Convert",
			delete: "Delete",
			active: "Active",
			inactive: "Inactive",
			conversionMayTakeTimeMsg:
				"Conversion may take time. Once its done, we will notify you!",
			convertedAllImageUploadDir:
				"Converted all Images inside upload directory.",
			convertedAllImageThemeDir: "Converted all Images inside theme directory.",
			operationFailedPhpExeTime:
				"Operation failed, Unable to set php execution time limit.",
			thisMayTakeTime:
				"This may take time. Once it's done, we will notify you!",
			deleteImageInUpload: "Deleted all Avif Images inside upload directory.",
			deleteImageInTheme: "Deleted all Avif Images inside the Theme directory.",
			qualityWarning:
				"0 - Worst, 100 - Best. High quality will increase the file size.",
			speedWarning: "0 - Super slow, smaller file. 10 - Fast, larger file.",
		},
		es: {
			pluginName: "Avif expreso",
			tagline:
				"Convierta imágenes existentes y recién cargadas al formato AVIF ligero.",
			hireMe: "contratame",
			autoConvertLabel:
				"Convierta automáticamente las imágenes al formato AVIF al cargarlas.",
			rendering: "Mostrar en el sitio web",
			iamgeQuality: "Calidad de la imagen",
			compressionSpeed: "Velocidad de compresión",
			uploadDirectory: "Upload directorio",
			themeDirectory: "Directorio de temas",
			convert: "Convertir",
			delete: "Borrar",
			active: "Activo",
			inactive: "Inactivo",
			conversionMayTakeTimeMsg:
				"La conversión puede llevar tiempo. Una vez hecho esto, ¡te lo haremos saber!",
			convertedAllImageUploadDir:
				"Convirtió todas las imágenes dentro del directorio de carga.",
			convertedAllImageThemeDir:
				"Convirtió todas las imágenes dentro del directorio del tema.",
			operationFailedPhpExeTime:
				"La operación falló, no se pudo establecer el límite de tiempo de ejecución de php.",
			thisMayTakeTime:
				"Esto puede llevar tiempo. Una vez que esté hecho, ¡te lo haremos saber!",
			deleteImageInUpload:
				"Se eliminaron todas las imágenes de Avif dentro del directorio de carga.",
			deleteImageInTheme:
				"Se eliminaron todas las imágenes de Avif dentro del directorio de temas.",
			qualityWarning:
				"0 - Peor, 100 - Mejor. La alta calidad aumentará el tamaño del archivo.",
			speedWarning:
				"0 - Súper lento, archivo más pequeño. 10 - Archivo rápido y más grande.",
		},
		bn: {
			pluginName: "এভিআইএফ এক্সপ্রেস",
			tagline:
				"বিদ্যমান এবং নতুন আপলোড করা ছবিকে লাইট-ওয়েট AVIF ফরম্যাটে রূপান্তর করে।",
			hireMe: "যোগযোগ",
			autoConvertLabel:
				"আপলোড করার সময় স্বয়ংক্রিয়ভাবে ছবিগুলিকে AVIF ফর্ম্যাটে রূপান্তর করুন৷",
			rendering: "ওয়েব পেজে দেখান",
			iamgeQuality: "ছবির মান",
			compressionSpeed: "কম্প্রেশন গতি",
			uploadDirectory: "আপলোড ডিরেক্টরি",
			themeDirectory: "থিম ডিরেক্টরি",
			convert: "রূপান্তর করুন",
			delete: "মুছে ফেলুন",
			active: "সক্রিয়",
			inactive: "নিষ্ক্রিয়",
			conversionMayTakeTimeMsg:
				"রূপান্তর সময় লাগতে পারে. একবার এটি হয়ে গেলে, আমরা আপনাকে অবহিত করব!",
			convertedAllImageUploadDir:
				"আপলোড ডিরেক্টরির ভিতরে সমস্ত চিত্র রূপান্তরিত।",
			convertedAllImageThemeDir: "থিম ডিরেক্টরির ভিতরে সমস্ত চিত্র রূপান্তরিত।",
			operationFailedPhpExeTime:
				"অপারেশন ব্যর্থ হয়েছে, পিএইচপি সম্পাদনের সময়সীমা সেট করতে অক্ষম৷",
			thisMayTakeTime:
				"এতে সময় লাগতে পারে। একবার এটি সম্পন্ন হলে, আমরা আপনাকে অবহিত করব!",
			deleteImageInUpload:
				"আপলোড ডিরেক্টরির মধ্যে থাকা সমস্ত Avif ছবি মুছে ফেলা হয়েছে।",
			deleteImageInTheme:
				"থিম ডিরেক্টরির মধ্যে থাকা সমস্ত Avif ছবি মুছে ফেলা হয়েছে।",
			qualityWarning:
				"০ - সবচেয়ে খারাপ, ১০০ - সেরা। উচ্চ মানের ফাইল আকার বৃদ্ধি হবে.",
			speedWarning: "০ - অতি ধীর, ছোট ফাইল। ১০ - দ্রুত, বড় ফাইল।",
		},
		de: {
			pluginName: "Avif-Express",
			tagline:
				"Konvertiert vorhandene und neu hochgeladene Bilder in das leichte AVIF-Format.",
			hireMe: "stellt mich ein",
			autoConvertLabel:
				"Konvertieren Sie Bilder beim Hochladen automatisch in das AVIF-Format.",
			rendering: "Rendern",
			iamgeQuality: "Bildqualität",
			compressionSpeed: "Kompressions Geschwindigkeit",
			uploadDirectory: "Verzeichnis hochladen",
			themeDirectory: "Thema Verzeichnis",
			convert: "Konvertieren",
			delete: "Löschen",
			active: "Aktiv",
			inactive: "Inaktiv",
			conversionMayTakeTimeMsg:
				"Die Konvertierung kann einige Zeit in Anspruch nehmen. Sobald es fertig ist, werden wir Sie benachrichtigen!",
			convertedAllImageUploadDir:
				"Alle Bilder im Upload-Verzeichnis konvertiert.",
			convertedAllImageThemeDir:
				"Alle Bilder im Themenverzeichnis konvertiert.",
			operationFailedPhpExeTime:
				"Vorgang fehlgeschlagen, PHP-Ausführungszeitlimit kann nicht festgelegt werden.",
			thisMayTakeTime:
				"Das kann ein bisschen dauern. Sobald es fertig ist, werden wir Sie benachrichtigen!",
			deleteImageInUpload: "Alle Avif-Bilder im Upload-Verzeichnis gelöscht.",
			deleteImageInTheme: "Alle Avif-Bilder im Theme-Verzeichnis gelöscht.",
			qualityWarning:
				"0 - Am schlechtesten, 100 - Am besten. Hohe Qualität erhöht die Dateigröße.",
			speedWarning:
				"0 - Super langsame, kleinere Datei. 10 - Schnelle, größere Datei.",
		},
		hi: {
			pluginName: "एवीआईएफ एक्सप्रेस",
			tagline:
				"मौजूदा और नई अपलोड की गई छवियों को लाइट-वेट एवीआईएफ प्रारूप में परिवर्तित करता है।",
			hireMe: "मुझे चुनिएँ",
			autoConvertLabel:
				"अपलोड होने पर स्वचालित रूप से छवियों को एवीआईएफ प्रारूप में परिवर्तित करें।",
			rendering: "प्रतिपादन",
			iamgeQuality: "छवि के गुणवत्ता",
			compressionSpeed: "संपीड़न गति",
			uploadDirectory: "अपलोड फ़ोल्डर",
			themeDirectory: "थीम फ़ोल्डर",
			convert: "बदलना",
			delete: "मिटाना",
			active: "सक्रिय",
			inactive: "निष्क्रिय",
			conversionMayTakeTimeMsg:
				"रूपांतरण में समय लग सकता है। एक बार यह हो जाने के बाद, हम आपको सूचित करेंगे!",
			convertedAllImageUploadDir:
				"सभी छवियों को अपलोड फ़ोल्डर के अंदर परिवर्तित कर दिया।",
			convertedAllImageThemeDir:
				"सभी छवियों को थीम फ़ोल्डर के अंदर परिवर्तित कर दिया।",
			operationFailedPhpExeTime:
				"कार्रवाई विफल, php निष्पादन समय सीमा निर्धारित करने में असमर्थ।",
			thisMayTakeTime:
				"इसमें समय लग सकता है। एक बार यह हो जाने के बाद, हम आपको सूचित करेंगे!",
			deleteImageInUpload: "अपलोड फ़ोल्डर के अंदर सभी एविफ छवियां मिटा दी गया।",
			deleteImageInTheme:
				"थीम फोल्डर के अंदर सभी एविफ छवियों को मिटा दिया गया।",
			qualityWarning:
				"० - सबसे खराब, १०० - सबसे अच्छा। उच्च गुणवत्ता फ़ाइल का आकार बढ़ाएगी।",
			speedWarning: "० - अत्यंत धीमी, छोटी फ़ाइल। १० - तेज़, बड़ी फ़ाइल।",
		},
		fr: {
			pluginName: "Avif express",
			tagline:
				"Convertit les images existantes et nouvellement upload au format AVIF léger.",
			hireMe: "engagez moi",
			autoConvertLabel:
				"Convertissez automatiquement les images au format AVIF lors du upload.",
			rendering: "Le rendu",
			iamgeQuality: "Qualité d'image",
			compressionSpeed: "Vitesse de compression",
			uploadDirectory: "Répertoire des Upload",
			themeDirectory: "Répertoire des thèmes",
			convert: "Convertir",
			delete: "Supprimer",
			active: "Actif",
			inactive: "Inactif",
			conversionMayTakeTimeMsg:
				"La conversion peut prendre un certain temps. Une fois que c'est fait, nous vous tiendrons au courant!",
			convertedAllImageUploadDir:
				"Conversion de toutes les images dans le répertoire de upload.",
			convertedAllImageThemeDir:
				"Conversion de toutes les images dans le répertoire du thème.",
			operationFailedPhpExeTime:
				"Échec de l'opération, impossible de définir la limite de temps d'exécution de php.",
			thisMayTakeTime:
				"Cela peut prendre du temps. Une fois que c'est fait, nous vous informerons!",
			deleteImageInUpload:
				"Suppression de toutes les images Avif dans le répertoire de Upload.",
			deleteImageInTheme:
				"Suppression de toutes les images Avif dans le répertoire Thème.",
			qualityWarning:
				"0 - Pire, 100 - Meilleur. La haute qualité augmentera la taille du fichier.",
			speedWarning:
				"0 - Fichier super lent et plus petit. 10 - Fichier rapide et plus volumineux.",
		},
		ru: {
			pluginName: "Авиф Экспресс",
			tagline:
				"Преобразует существующие и недавно загруженные изображения в облегченный формат AVIF.",
			hireMe: "найми меня",
			autoConvertLabel:
				"Автоматически конвертировать изображения в формат AVIF при загрузке.",
			rendering: "Рендеринг",
			iamgeQuality: "Качество изображения",
			compressionSpeed: "Скорость сжатия",
			uploadDirectory: "Загрузить каталог",
			themeDirectory: "Каталог тем",
			convert: "Конвертировать",
			delete: "Удалить",
			active: "Активный",
			inactive: "Неактивный",
			conversionMayTakeTimeMsg:
				"Преобразование может занять время. Как только это будет сделано, мы сообщим вам!",
			convertedAllImageUploadDir:
				"Преобразованы все изображения внутри каталога загрузки.",
			convertedAllImageThemeDir:
				"Преобразованы все изображения внутри каталога темы.",
			operationFailedPhpExeTime:
				"Операция не удалась, невозможно установить ограничение времени выполнения php.",
			thisMayTakeTime:
				"Это может занять время. Как только это будет сделано, мы сообщим вам!",
			deleteImageInUpload:
				"Удалены все изображения Avif внутри каталога загрузки.",
			deleteImageInTheme: "Удалены все изображения Avif в каталоге Theme.",
			qualityWarning:
				"0 - Худший, 100 - Лучший. Высокое качество увеличит размер файла.",
			speedWarning:
				"0 - очень медленный, файл меньшего размера. 10 - Быстрый файл большего размера.",
		},
	},
});

export default i18n;
