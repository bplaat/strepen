class User {
  final int id;
  final String firstname;
  final String? insertion;
  final String lastname;
  final String? avatar;
  final double? balance;

  User({
    required this.id,
    required this.firstname,
    required this.insertion,
    required this.lastname,
    required this.avatar,
    required this.balance
  });

  String get name {
    if (insertion != null) {
      return firstname + ' ' + insertion! + ' ' + lastname;
    }
    return firstname + ' ' + lastname;
  }

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      firstname: json['firstname'],
      insertion: json['insertion'],
      lastname: json['lastname'],
      avatar: json['avatar'],
      balance: json['balance']?.toDouble()
    );
  }
}
