"use client";

import Link from "next/link"
import { ArrowRight, Diamond, Shield, Wallet, Zap } from "lucide-react"
import { useTonWallet, useTonConnectUI } from '@tonconnect/ui-react';
import { useEffect, useState } from 'react';

import { Button } from "@/components/ui/button"
import { WalletConnectButton } from "@/components/wallet-connect-button"
import { useToast } from "@/components/ui/use-toast";
import { api } from "../services/api";
import { Asset } from "@/app/types";
import { useTonTransaction } from "@/app/utils/ton-transaction";

// Simple seeded random number generator
function seededRandom(seed: number) {
  const x = Math.sin(seed++) * 10000;
  return x - Math.floor(x);
}

export default function Home() {
  const wallet = useTonWallet();
  const [tonConnectUI] = useTonConnectUI();
  const { toast } = useToast();
  const { placeBid } = useTonTransaction();
  const [assets, setAssets] = useState<Asset[]>([]);
  const [auctionAssets, setAuctionAssets] = useState<Asset[]>([]);
  const [loading, setLoading] = useState(true);
  const [loadingAuctions, setLoadingAuctions] = useState(true);

  useEffect(() => {
    const fetchAssets = async () => {
      try {
        const data = await api.getAssets();
        setAssets(data);
      } catch (error) {
        console.error("Failed to fetch assets:", error);
        toast({
          title: "Error",
          description: "Failed to fetch assets",
          variant: "destructive",
        });
      } finally {
        setLoading(false);
      }
    };

    const fetchAuctions = async () => {
      try {
        const data = await api.getAuctionAssets();
        setAuctionAssets(data);
      } catch (error) {
        console.error("Failed to fetch auctions:", error);
        toast({
          title: "Error",
          description: "Failed to fetch auctions",
          variant: "destructive",
        });
      } finally {
        setLoadingAuctions(false);
      }
    };

    fetchAssets();
    fetchAuctions();
  }, [toast]);

  const handlePlaceBid = async (assetId: number, amount: string) => {
    if (!wallet) {
      toast({
        title: "Wallet not connected",
        description: "Please connect your TON wallet to place a bid",
        variant: "destructive",
      });
      tonConnectUI.openModal();
      return;
    }

    try {
      // For frontend-only demo, we'll just update via the API
      const transaction = await api.placeBid(
        assetId,
        wallet.account.address,
        parseFloat(amount) + 1 // Add 1 TON to current price
      );

      toast({
        title: "Bid placed",
        description: `Your bid of ${parseFloat(amount) + 1} TON has been placed`,
      });

      // Refresh assets
      const updatedAssets = await api.getAssets();
      setAssets(updatedAssets);
      
      // Refresh auction assets
      const updatedAuctions = await api.getAuctionAssets();
      setAuctionAssets(updatedAuctions);
    } catch (error: any) {
      toast({
        title: "Error",
        description: error.message || "Failed to place bid",
        variant: "destructive",
      });
    }
  };
  
  return (
    <div className="flex min-h-screen flex-col">
      <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div className="container flex h-16 items-center justify-between">
          <div className="flex items-center gap-6 md:gap-10">
            <Link href="/" className="flex items-center space-x-2">
              <Diamond className="h-6 w-6" />
              <span className="font-bold">Ton Store</span>
            </Link>
            <nav className="hidden gap-6 md:flex">
              <Link href="#marketplace" className="text-sm font-medium transition-colors hover:text-primary">
                Marketplace
              </Link>
              <Link href="#auctions" className="text-sm font-medium transition-colors hover:text-primary">
                Auctions
              </Link>
              <Link href="#domains" className="text-sm font-medium transition-colors hover:text-primary">
                Domains
              </Link>
              <Link href="#about" className="text-sm font-medium transition-colors hover:text-primary">
                About
              </Link>
              <Link href="/wallet" className="text-sm font-medium transition-colors hover:text-primary flex items-center gap-1">
                <Wallet className="h-4 w-4" />
                Wallet Demo
              </Link>
            </nav>
          </div>
          <div className="flex items-center gap-4">
            <Link href="/admin" className="text-sm font-medium transition-colors hover:text-primary">
              Admin
            </Link>
            <WalletConnectButton />
          </div>
        </div>
      </header>
      <main className="flex-1">
        <section className="w-full py-12 md:py-24 lg:py-32 xl:py-48">
          <div className="container px-4 md:px-6">
            <div className="grid gap-6 lg:grid-cols-[1fr_400px] lg:gap-12 xl:grid-cols-[1fr_600px]">
              <div className="flex flex-col justify-center space-y-4">
                <div className="space-y-2">
                  <h1 className="text-3xl font-bold tracking-tighter sm:text-5xl xl:text-6xl/none">
                    The Premier Marketplace for TON Digital Assets
                  </h1>
                  <p className="max-w-[600px] text-muted-foreground md:text-xl">
                    Buy, sell, and auction digital assets on the TON blockchain. Secure, fast, and user-friendly.
                  </p>
                </div>
                <div className="flex flex-col gap-2 min-[400px]:flex-row">
                  <Button size="lg">
                    Explore Marketplace
                    <ArrowRight className="ml-2 h-4 w-4" />
                  </Button>
                  <Button size="lg" variant="outline">
                    Learn More
                  </Button>
                  <Link href="/wallet">
                    <Button size="lg" variant="secondary">
                      <Wallet className="mr-2 h-4 w-4" />
                      Try Wallet Demo
                    </Button>
                  </Link>
                </div>
              </div>
              <div className="flex items-center justify-center">
                <div className="relative h-[450px] w-[450px] rounded-full bg-gradient-to-r from-primary/20 to-primary p-1">
                  <div className="absolute inset-0 flex items-center justify-center rounded-full bg-background p-6">
                    <div className="space-y-2 text-center">
                      <div className="text-4xl font-bold">10,000+</div>
                      <div className="text-sm text-muted-foreground">Digital Assets Available</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section id="marketplace" className="w-full py-12 md:py-24 lg:py-32 bg-muted/50">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center justify-center space-y-4 text-center">
              <div className="space-y-2">
                <h2 className="text-3xl font-bold tracking-tighter sm:text-5xl">Featured Assets</h2>
                <p className="max-w-[900px] text-muted-foreground md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed">
                  Discover the most popular digital assets on the TON blockchain
                </p>
              </div>
            </div>
            <div className="mx-auto grid max-w-5xl grid-cols-1 gap-6 py-12 md:grid-cols-2 lg:grid-cols-3">
              {loading ? (
                <div className="col-span-full text-center">Loading assets...</div>
              ) : (
                assets.map((asset) => (
                  <div
                    key={asset.id}
                    className="group relative overflow-hidden rounded-lg border bg-background shadow-md transition-all hover:shadow-xl"
                  >
                    <div className="aspect-square overflow-hidden">
                      {asset.image_url ? (
                        <img
                          src={asset.image_url}
                          alt={asset.name}
                          className="h-full w-full object-cover"
                        />
                      ) : (
                        <div className="h-full w-full bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20"></div>
                      )}
                    </div>
                    <div className="p-4">
                      <h3 className="font-semibold">{asset.name}</h3>
                      <p className="mt-1 text-sm text-muted-foreground">{asset.description}</p>
                      <div className="mt-2 flex items-center justify-between">
                        <span className="text-sm text-muted-foreground">Current bid</span>
                        <span className="font-medium">{asset.price} TON</span>
                      </div>
                      <Button 
                        className="mt-4 w-full" 
                        size="sm"
                        onClick={() => handlePlaceBid(asset.id, asset.price.toString())}
                      >
                        {wallet ? 'Place Bid' : 'Connect Wallet to Bid'}
                      </Button>
                    </div>
                  </div>
                ))
              )}
            </div>
            <div className="flex justify-center">
              <Button variant="outline" size="lg">
                View All Assets
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </div>
          </div>
        </section>
        <section id="auctions" className="w-full py-12 md:py-24 lg:py-32">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center justify-center space-y-4 text-center">
              <div className="space-y-2">
                <h2 className="text-3xl font-bold tracking-tighter sm:text-5xl">Live Auctions</h2>
                <p className="max-w-[900px] text-muted-foreground md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed">
                  Bid on exclusive digital assets before they're gone
                </p>
              </div>
            </div>
            <div className="mx-auto grid max-w-5xl grid-cols-1 gap-8 py-12 md:grid-cols-2">
              {loadingAuctions ? (
                <div className="col-span-full text-center">Loading auctions...</div>
              ) : (
                auctionAssets.map((auction) => (
                  <div
                    key={auction.id}
                    className="group relative overflow-hidden rounded-lg border bg-background p-6 shadow-md transition-all hover:shadow-xl"
                  >
                    <div className="flex items-start justify-between">
                      <div>
                        <h3 className="text-xl font-semibold">{auction.name}</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          {auction.auction_end_time 
                            ? `Ends on ${new Date(auction.auction_end_time).toLocaleDateString()}`
                            : 'Auction ends in 2 days'}
                        </p>
                      </div>
                      <div className="rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">Hot</div>
                    </div>
                    <div className="mt-6 flex items-end justify-between">
                      <div>
                        <p className="text-sm text-muted-foreground">Current bid</p>
                        <p className="text-2xl font-bold">{auction.price.toFixed(2)} TON</p>
                      </div>
                      <Button onClick={() => handlePlaceBid(auction.id, auction.price.toString())}>
                        {wallet ? 'Place Bid' : 'Connect to Bid'}
                      </Button>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>
        </section>
        <section id="domains" className="w-full py-12 md:py-24 lg:py-32 bg-muted/50">
          <div className="container px-4 md:px-6">
            <div className="grid gap-6 lg:grid-cols-[1fr_400px] lg:gap-12 xl:grid-cols-[1fr_600px]">
              <div className="flex flex-col justify-center space-y-4">
                <div className="space-y-2">
                  <h2 className="text-3xl font-bold tracking-tighter sm:text-5xl">TON Domains</h2>
                  <p className="max-w-[600px] text-muted-foreground md:text-xl">
                    Secure your digital identity with a .ton domain name. Easy to remember, impossible to forge.
                  </p>
                </div>
                <ul className="grid gap-2">
                  <li className="flex items-center gap-2">
                    <Shield className="h-4 w-4 text-primary" />
                    <span>Secure and decentralized</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <Zap className="h-4 w-4 text-primary" />
                    <span>Fast and reliable</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <Diamond className="h-4 w-4 text-primary" />
                    <span>Valuable digital asset</span>
                  </li>
                </ul>
                <div>
                  <Button size="lg">
                    Register Domain
                    <ArrowRight className="ml-2 h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div className="flex items-center justify-center">
                <div className="w-full max-w-md space-y-4 rounded-lg border bg-background p-6 shadow-lg">
                  <div className="space-y-2">
                    <h3 className="text-xl font-semibold">Search for a domain</h3>
                    <p className="text-sm text-muted-foreground">Find your perfect .ton domain name</p>
                  </div>
                  <div className="flex gap-2">
                    <div className="relative flex-1">
                      <input
                        type="text"
                        placeholder="Enter domain name"
                        className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                      />
                      <div className="absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-muted-foreground">
                        .ton
                      </div>
                    </div>
                    <Button type="submit">Search</Button>
                  </div>
                  <div className="space-y-2">
                    <div className="flex items-center justify-between rounded-md border p-2">
                      <span>example.ton</span>
                      <span className="text-sm font-medium text-green-600">Available</span>
                    </div>
                    <div className="flex items-center justify-between rounded-md border p-2">
                      <span>premium.ton</span>
                      <span className="text-sm font-medium text-destructive">Taken</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section id="about" className="w-full py-12 md:py-24 lg:py-32">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center justify-center space-y-4 text-center">
              <div className="space-y-2">
                <h2 className="text-3xl font-bold tracking-tighter sm:text-5xl">About Ton Store</h2>
                <p className="max-w-[900px] text-muted-foreground md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed">
                  The premier marketplace for digital assets on the TON blockchain
                </p>
              </div>
              <div className="mx-auto max-w-3xl text-center">
                <p className="mb-4">
                  Ton Store is a decentralized marketplace built on the TON blockchain, offering a secure and
                  user-friendly platform for buying, selling, and auctioning digital assets.
                </p>
                <p>
                  Our mission is to make blockchain technology accessible to everyone, providing a seamless experience
                  for both crypto enthusiasts and newcomers alike.
                </p>
              </div>
            </div>
          </div>
        </section>
      </main>
      <footer className="w-full border-t bg-background py-6">
        <div className="container px-4 md:px-6">
          <div className="flex flex-col items-center justify-between gap-4 md:flex-row">
            <div className="flex items-center gap-2">
              <Diamond className="h-5 w-5" />
              <span className="font-semibold">Ton Store</span>
            </div>
            <nav className="flex gap-4 sm:gap-6">
              <Link href="#" className="text-sm font-medium hover:underline">
                Terms
              </Link>
              <Link href="#" className="text-sm font-medium hover:underline">
                Privacy
              </Link>
              <Link href="#" className="text-sm font-medium hover:underline">
                Contact
              </Link>
            </nav>
            <div className="text-sm text-muted-foreground">
              © {new Date().getFullYear()} Ton Store. All rights reserved.
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}

