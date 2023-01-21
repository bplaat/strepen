class Organisation {
  final int id;
  final String name;
  final String url;
  final String apiKey;

  const Organisation({
    required this.id,
    required this.name,
    required this.url,
    required this.apiKey
  });

  String get host {
    return Uri.parse(url).host;
  }

  String get apiUrl {
    return url + 'api';
  }
}
