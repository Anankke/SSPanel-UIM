{
  description = "SSPanel V3 UIM mod";
  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixpkgs-unstable";
    flake-utils = {
      url = "github:numtide/flake-utils";
      inputs.nixpkgs.follows = "nixpkgs";
    };
  };

  outputs = { self, nixpkgs, flake-utils }:
    flake-utils.lib.eachSystem [ "armv7l-linux" "x86_64-linux" "x86_64-darwin" ] (system: let
      pkgs = import nixpkgs {
        system = system;
      };
    in
    {
      devShell = (pkgs.mkShell {
        buildInputs = with pkgs; with php80Packages; let
          phpWithExtensions = php.withExtensions ({ enabled, all }:
            enabled ++ [ all.imagick all.xdebug ]);
        in [
          phpWithExtensions composer
        ];
      });
    }
  );
}
