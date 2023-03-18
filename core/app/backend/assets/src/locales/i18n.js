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
	},
});

export default i18n;
