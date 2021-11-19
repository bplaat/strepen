class User {
  final int id;
  final String firstname;
  final String? insertion;
  final String lastname;
  final String? avatar;
  final String? thanks;
  double? balance;
  final bool? minor;

  User({
    required this.id,
    required this.firstname,
    required this.insertion,
    required this.lastname,
    required this.avatar,
    required this.thanks,
    required this.balance,
    required this.minor
  });

  String get name {
    if (insertion != null) {
      return '${firstname} ${insertion!} ${lastname}';
    }
    return '${firstname} ${lastname}';
  }

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      firstname: json['firstname'],
      insertion: json['insertion'],
      lastname: json['lastname'],
      avatar: json['avatar'],
      thanks: json['thanks'],
      balance: json['balance']?.toDouble(),
      minor: json['minor']
    );
  }
}
